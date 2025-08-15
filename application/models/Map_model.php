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
    
    public function kelurahan_list(){ 
        // Dapatkan daftar kelurahan dari tabel geojson_data (data lama)
      
        // Dapatkan daftar file KML dari tabel kml_data_head (data baru)
        $this->db->select('id, name as kelurahan')->from($this->kml_head_table); 
        $this->db->order_by('name', 'ASC'); 
        $kml_files = $this->db->get()->result();
        
        // Gabungkan data
        return $kml_files;
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
    
    // Fungsi untuk mengambil polygon berdasarkan ID
    public function get_polygon_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
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