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
        $data['default_kelurahan'] = 'Karimun'; // Set default kelurahan to Karimun
        $data['load_leaflet_draw'] = true; // Memastikan Leaflet.draw dimuat
        $this->load->view('layouts/header',$data); 
        $this->load->view('map_view',$data); 
        $this->load->view('layouts/footer'); 
    }
    
    public function data(){ 
        $kel=$this->input->get('kelurahan',TRUE); 
        $rows=$this->mm->get_all($kel); 
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows)); 
    }
    
    // Fungsi baru untuk mendapatkan data KML berdasarkan ID file
    public function kml_data($head_id){ 
        $rows = $this->mm->get_kml_data_by_head_id($head_id); 
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