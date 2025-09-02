<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Map extends CI_Controller {
    public function __construct(){ 
        parent::__construct(); 
        $this->load->model('Map_model','mm'); 
        $this->load->helper(['url','auth']); 
        $this->load->library('session'); 
        require_login(); 
    }
    
    public function index(){ 
        $data['user']=current_user(); 
        $data['kelurahan_list']=$this->mm->kelurahan_list(); 
        $data['kecamatan_list']=$this->mm->kecamatan_list(); // Menambahkan daftar kecamatan
        $data['default_kelurahan'] = 'Karimun'; // Set default kelurahan to Karimun
        $data['load_leaflet_draw'] = true; // Memastikan Leaflet.draw dimuat
        $this->load->view('layouts/header',$data); 
        $this->load->view('map_view',$data); 
        $this->load->view('layouts/footer'); 
    }
    
    public function data(){ 
        $kel = $this->input->get('kelurahan', TRUE);
        $kec = $this->input->get('kecamatan', TRUE);
        $polygon_id = $this->input->get('polygon_id', TRUE);
        
        if ($polygon_id) {
            // Jika ada ID polygon spesifik
            $rows = $this->mm->get_polygon_by_id($polygon_id);
            // Kembalikan dalam format array
            $rows = $rows ? [$rows] : [];
        } else if ($kec) {
            // Jika ada filter kecamatan
            $rows = $this->mm->get_by_kecamatan($kec);
        } else {
            // Jika tidak ada filter kecamatan, gunakan filter kelurahan seperti biasa
            $rows = $this->mm->get_all($kel);
        }
        
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }
    
    // Fungsi untuk mendukung pencarian polygon dengan Select2 (server-side)
    public function search_polygon() {
        $term = $this->input->get('term', TRUE);
        $page = $this->input->get('page', TRUE) ?: 1;
        $categories = $this->input->get('categories', TRUE); // Mendapatkan kategori yang dipilih
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Dapatkan hasil pencarian dari model
        $results = $this->mm->search_polygon($term, $limit, $offset, $categories);
        $total_count = $this->mm->count_search_polygon($term, $categories);
        
        $data = [];
        foreach ($results as $row) {
            // Tambahkan informasi tambahan
            $data[] = [
                'id' => $row->id,
                'text' => $row->name,
                'kelurahan' => isset($row->kelurahan) ? $row->kelurahan : '',
                'kategori' => isset($row->kategori) ? $row->kategori : '',
                'head_id' => isset($row->head_id) ? $row->head_id : null // Tambahkan head_id
            ];
        }
        
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'results' => $data,
                'pagination' => [
                    'more' => ($page * $limit) < $total_count
                ]
            ]));
    }
    
    // Fungsi baru untuk mendapatkan data KML berdasarkan ID file
    public function kml_data($head_id){ 
        $rows = $this->mm->get_kml_data_by_head_id($head_id); 
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows)); 
    }
    
    // Fungsi untuk mendapatkan daftar tipe poligon dalam file KML
    public function kml_polygon_types($head_id) {
        $this->load->model('Kml_model');
        $types = $this->Kml_model->get_polygon_types($head_id);
        return $this->output->set_content_type('application/json')->set_output(json_encode($types));
    }
    
    // Fungsi untuk mendapatkan data KML berdasarkan ID file dan tipe poligon
    public function kml_data_by_types() {
        $head_id = $this->input->get('head_id');
        $types = $this->input->get('types'); // Array of types
        
        $this->load->model('Kml_model');
        if ($types && is_array($types)) {
            $rows = $this->Kml_model->get_detail_data_by_types($head_id, $types);
        } else {
            $rows = $this->Kml_model->get_detail_data($head_id);
        }
        
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }
    
    // Fungsi untuk mendapatkan daftar kategori poligon dalam file KML
    public function kml_polygon_categories($head_id) {
        $this->load->model('Kml_model');
        $categories = $this->Kml_model->get_polygon_categories($head_id);
        return $this->output->set_content_type('application/json')->set_output(json_encode($categories));
    }
    
    // Fungsi untuk mendapatkan daftar kategori poligon dalam file KML berdasarkan filter
    public function kml_filtered_data() {
        $head_id = $this->input->get('head_id');
        $categories = $this->input->get('categories'); // Array of categories
        
        $this->load->model('Kml_model');
        if ($categories && is_array($categories)) {
            $rows = $this->Kml_model->get_detail_data_by_categories($head_id, $categories);
        } else {
            $rows = $this->Kml_model->get_detail_data($head_id);
        }
        
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }
    
    // Fungsi untuk menyimpan polygon yang digambar langsung di peta
    public function save_polygon() {
        // Pastikan hanya admin yang bisa menyimpan polygon
        if (!is_admin()) {
            show_error('Forbidden', 403);
            return;
        }
        
        // Dapatkan data dari POST
        $name = $this->input->post('name');
        $kelurahan = $this->input->post('kelurahan');
        $geometry = $this->input->post('geometry');
        $color = $this->input->post('color', TRUE) ?: '#3388ff';
        
        if (!$name || !$geometry || !$kelurahan) {
            return $this->output->set_status_header(400)->set_output('Name, kelurahan, and geometry are required');
        }
        
        // Cek apakah kelurahan yang dipilih adalah ID file KML
        if (is_numeric($kelurahan)) {
            // Jika ya, simpan ke tabel kml_data_detail
            $data = [
                'head_id' => $kelurahan, // ID file KML sebagai head_id
                'kelurahan' => $name,
                'kategori' => 'Drawn Polygon',
                'name' => $name,
                'color' => $color,
                'geometry' => $geometry,
                'properties' => json_encode(['source' => 'manual_draw'])
            ];
            
            $this->load->model('Kml_model');
            $this->Kml_model->save_kml_detail($data);
        } else {
            // Jika tidak, simpan ke tabel geojson_data (data lama)
            $data = [
                'kelurahan' => $kelurahan,
                'kategori' => 'Drawn Polygon',
                'name' => $name,
                'color' => $color,
                'geometry' => $geometry,
                'properties' => json_encode(['source' => 'manual_draw'])
            ];
            
            $this->load->model('Map_model');
            $this->Map_model->insert_polygon($data);
        }
        
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success']));
    }
    
    // Fungsi untuk mengupdate polygon
    public function update_polygon($id) {
        // Pastikan hanya admin yang bisa mengupdate polygon
        if (!is_admin()) {
            show_error('Forbidden', 403);
            return;
        }
        
        if (!$id) {
            return $this->output->set_status_header(400)->set_output('ID is required');
        }
        
        // Dapatkan data dari POST
        $geometry = $this->input->post('geometry');
        $color = $this->input->post('color', TRUE) ?: '#3388ff';
        
        if (!$geometry) {
            return $this->output->set_status_header(400)->set_output('Geometry is required');
        }
        
        // Cek apakah polygon ada di tabel kml_data_detail
        $this->load->model('Kml_model');
        $kml_data = $this->Kml_model->get_kml_detail_by_id($id);
        
        if ($kml_data) {
            // Update di tabel kml_data_detail
            $data = [
                'geometry' => $geometry,
                'color' => $color
            ];
            
            $this->Kml_model->update_kml_detail($id, $data);
        } else {
            // Cek apakah polygon ada di tabel geojson_data
            $this->load->model('Map_model');
            $geojson_data = $this->Map_model->get_polygon_by_id($id);
            
            if ($geojson_data) {
                // Update di tabel geojson_data
                $data = [
                    'geometry' => $geometry,
                    'color' => $color
                ];
                
                $this->Map_model->update_polygon($id, $data);
            } else {
                return $this->output->set_status_header(404)->set_output('Polygon not found');
            }
        }
        
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success']));
    }
    
    // Fungsi untuk menghapus polygon
    public function delete_polygon($id) {
        // Pastikan hanya admin yang bisa menghapus polygon
        if (!is_admin()) {
            show_error('Forbidden', 403);
            return;
        }
        
        if (!$id) {
            return $this->output->set_status_header(400)->set_output('ID is required');
        }
        
        // Cek apakah polygon ada di tabel kml_data_detail
        $this->load->model('Kml_model');
        $kml_data = $this->Kml_model->get_kml_detail_by_id($id);
        
        if ($kml_data) {
            // Hapus dari tabel kml_data_detail
            $this->Kml_model->delete_kml_detail($id);
        } else {
            // Cek apakah polygon ada di tabel geojson_data
            $this->load->model('Map_model');
            $geojson_data = $this->Map_model->get_polygon_by_id($id);
            
            if ($geojson_data) {
                // Hapus dari tabel geojson_data
                $this->Map_model->delete_polygon($id);
            } else {
                return $this->output->set_status_header(404)->set_output('Polygon not found');
            }
        }
        
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success']));
    }
}