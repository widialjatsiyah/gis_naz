<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Map_model extends CI_Model {
    private $table='geojson_data';
    private $kml_head_table='kml_data_head';
    private $kml_detail_table='kml_data_detail';
    
    public function get_all($kel=null){ 
        // Dapatkan data dari tabel geojson_data (data lama)
        if($kel) $this->db->where('kelurahan',$kel); 
        $this->db->order_by('id','ASC'); 
        $old_data = $this->db->get($this->table)->result();
        
        // Dapatkan data dari tabel kml_data_detail (data KML baru)
        if($kel) $this->db->where('kelurahan',$kel); 
        $this->db->order_by('id','ASC'); 
        $kml_data = $this->db->get($this->kml_detail_table)->result();
        
        // Gabungkan data
        return array_merge($old_data, $kml_data);
    }
    
    // Fungsi untuk mendapatkan data berdasarkan kecamatan
    public function get_by_kecamatan($kecamatan_id) {
        // Dapatkan data dari tabel kml_data_detail yang terkait dengan kecamatan
        $this->db->select("kd.*, kh.kecamatan_id");
        $this->db->from("{$this->kml_detail_table} kd");
        $this->db->join("{$this->kml_head_table} kh", "kd.head_id = kh.id");
        $this->db->where("kh.kecamatan_id", $kecamatan_id);
        $this->db->order_by('kd.id', 'ASC');
        $kml_data = $this->db->get()->result();
        
        // Untuk data lama (geojson_data), kita tidak memiliki informasi kecamatan
        // Jadi hanya menampilkan data KML yang terkait dengan kecamatan
        
        return $kml_data;
    }
    
    // Fungsi untuk pencarian polygon (untuk Select2)
    public function search_polygon($term = '', $limit = 20, $offset = 0, $categories = null) {
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('name', $term);
            $this->db->or_like('kelurahan', $term);
            $this->db->group_end();
        }
        
        // Filter berdasarkan kategori jika ada
        if (!empty($categories) && is_array($categories)) {
            $this->db->group_start();
            foreach ($categories as $category) {
                $this->db->or_where('kategori', $category);
            }
            $this->db->group_end();
        }
        
        $this->db->order_by('name', 'ASC');
        $this->db->limit($limit, $offset);
        
        // Dapatkan data dari tabel geojson_data
        $old_data_query = $this->db->get($this->table);
        $old_data = $old_data_query->result();
        
        // Reset query untuk pencarian berikutnya
        $this->db->reset_query();
        
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('kd.name', $term);
            $this->db->or_like('kd.kelurahan', $term);
            $this->db->group_end();
        }
        
        // Filter berdasarkan kategori untuk data KML
        if (!empty($categories) && is_array($categories)) {
            $this->db->group_start();
            foreach ($categories as $category) {
                $this->db->or_where('kd.kategori', $category);
                // Juga filter berdasarkan TIPEHAK dalam properties
                $this->db->or_where("JSON_EXTRACT(kd.properties, '$.TIPEHAK')", $category);
            }
            $this->db->group_end();
        }
        
        $this->db->select("kd.*, kh.name as kelurahan_name");
        $this->db->from("{$this->kml_detail_table} kd");
        $this->db->join("{$this->kml_head_table} kh", "kd.head_id = kh.id", "left");
        $this->db->order_by('kd.name', 'ASC');
        $this->db->limit($limit, $offset);
        
        // Dapatkan data dari tabel kml_data_detail
        $kml_data_query = $this->db->get();
        $kml_data = $kml_data_query->result();
        
        // Gabungkan data
        $merged_data = array_merge($old_data, $kml_data);
        
        // Pastikan setiap item memiliki properti kelurahan dan kategori
        foreach ($merged_data as $item) {
            // Untuk data KML, gunakan kelurahan_name jika kelurahan tidak ada
            if (!isset($item->kelurahan) && isset($item->kelurahan_name)) {
                $item->kelurahan = $item->kelurahan_name;
            }
            
            // Pastikan kategori ada
            if (!isset($item->kategori)) {
                $item->kategori = '';
            }
        }
        
        return $merged_data;
    }
    
    // Fungsi untuk menghitung total hasil pencarian polygon
    public function count_search_polygon($term = '', $categories = null) {
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('name', $term);
            $this->db->or_like('kelurahan', $term);
            $this->db->group_end();
        }
        
        // Filter berdasarkan kategori jika ada
        if (!empty($categories) && is_array($categories)) {
            $this->db->group_start();
            foreach ($categories as $category) {
                $this->db->or_where('kategori', $category);
            }
            $this->db->group_end();
        }
        
        // Hitung data dari tabel geojson_data
        $old_data_count = $this->db->count_all_results($this->table, FALSE);
        
        // Reset query
        $this->db->reset_query();
        
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('kd.name', $term);
            $this->db->or_like('kd.kelurahan', $term);
            $this->db->group_end();
        }
        
        // Filter berdasarkan kategori untuk data KML
        if (!empty($categories) && is_array($categories)) {
            $this->db->group_start();
            foreach ($categories as $category) {
                $this->db->or_where('kd.kategori', $category);
                // Juga filter berdasarkan TIPEHAK dalam properties
                $this->db->or_where("JSON_EXTRACT(kd.properties, '$.TIPEHAK')", $category);
            }
            $this->db->group_end();
        }
        
        $this->db->from("{$this->kml_detail_table} kd");
        $this->db->join("{$this->kml_head_table} kh", "kd.head_id = kh.id", "left");
        
        // Hitung data dari tabel kml_data_detail
        $kml_data_count = $this->db->count_all_results();
        
        return $old_data_count + $kml_data_count;
    }
    
    public function kelurahan_list(){ 
        // Dapatkan daftar kelurahan dari tabel geojson_data (data lama)
      
        // Dapatkan daftar file KML dari tabel kml_data_head (data baru)
        $this->db->select('id, name as kelurahan, kecamatan_id')->from($this->kml_head_table); 
        $this->db->order_by('name', 'ASC'); 
        $kml_files = $this->db->get()->result();
        
        // Gabungkan data
        return $kml_files;
    }
    
    // Fungsi untuk mendapatkan daftar kecamatan
    public function kecamatan_list() {
        $this->load->model('Kml_model');
        return $this->Kml_model->get_all_kecamatan();
    }
    
    // Fungsi untuk mendapatkan detail data KML berdasarkan ID file
    public function get_kml_data_by_head_id($head_id) {
        $this->db->where('head_id', $head_id);
        $this->db->order_by('id', 'ASC');
        return $this->db->get($this->kml_detail_table)->result();
    }
    
    // Fungsi untuk menyimpan polygon yang digambar langsung di peta
    public function insert_polygon($data) {
        return $this->db->insert($this->table, $data);
    }
    
    // Fungsi untuk mendapatkan polygon berdasarkan ID
    public function get_polygon_by_id($id) {
        // Cari di tabel geojson_data terlebih dahulu
        $this->db->where('id', $id);
        $result = $this->db->get($this->table)->row();
        
        // Jika tidak ditemukan, cari di tabel kml_data_detail
        if (!$result) {
            $this->db->select("kd.*, kh.name as kelurahan_name");
            $this->db->from("{$this->kml_detail_table} kd");
            $this->db->join("{$this->kml_head_table} kh", "kd.head_id = kh.id", "left");
            $this->db->where("kd.id", $id);
            $result = $this->db->get()->row();
            
            // Sesuaikan nama field jika diperlukan
            if ($result && isset($result->kelurahan_name)) {
                $result->kelurahan = $result->kelurahan_name;
            }
        }
        
        return $result;
    }
    
    // Fungsi untuk mengupdate polygon
    public function update_polygon($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }
    
    // Fungsi untuk menghapus polygon
    public function delete_polygon($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}