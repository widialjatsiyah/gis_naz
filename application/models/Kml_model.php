<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kml_model extends CI_Model {
    private $head_table = 'kml_data_head';
    private $detail_table = 'kml_data_detail';
    
    public function create_tables_if_not_exists() {
        // Membuat tabel head
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->head_table}` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` varchar(255) DEFAULT NULL,
              `description` text,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");

        // Membuat tabel detail
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->detail_table}` (
              `id` int NOT NULL AUTO_INCREMENT,
              `head_id` int NOT NULL,
              `kelurahan` varchar(255) DEFAULT NULL,
              `kategori` varchar(100) DEFAULT NULL,
              `name` varchar(255) DEFAULT NULL,
              `color` varchar(20) NOT NULL DEFAULT '#3388ff',
              `properties` json DEFAULT NULL,
              `geometry` json NOT NULL,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `head_id` (`head_id`),
              CONSTRAINT `fk_kml_head` FOREIGN KEY (`head_id`) REFERENCES `{$this->head_table}` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");
    }
    
    public function save_kml_data($name, $description, $features) {
        // Simpan data head
        $head_data = [
            'name' => $name,
            'description' => $description
        ];
        
        $this->db->insert($this->head_table, $head_data);
        $head_id = $this->db->insert_id();
        
        // Simpan data detail
        foreach ($features as $feature) {
            $detail_data = [
                'head_id' => $head_id,
                'kelurahan' => $feature['kelurahan'],
                'kategori' => $feature['kategori'],
                'name' => $feature['name'],
                'color' => $feature['color'],
                'properties' => $feature['properties'],
                'geometry' => $feature['geometry']
            ];
            
            $this->db->insert($this->detail_table, $detail_data);
        }
        
        return $head_id;
    }
    
    // Fungsi baru untuk menyimpan data detail ke tabel kml_data_detail
    public function save_kml_detail($data) {
        return $this->db->insert($this->detail_table, $data);
    }
    
    public function get_all_kml() {
        return $this->db->get($this->head_table)->result_array();
    }
    
    public function get_all_head_data() {
        return $this->db->get($this->head_table)->result_array();
    }
    
    public function get_kml_by_id($id) {
        return $this->db->get_where($this->head_table, ['id' => $id])->row_array();
    }
    
    public function get_head_by_id($id) {
        return $this->db->get_where($this->head_table, ['id' => $id])->row_array();
    }
    
    public function get_kml_details($head_id) {
        return $this->db->get_where($this->detail_table, ['head_id' => $head_id])->result_array();
    }
    
    public function get_detail_data($head_id) {
        return $this->db->get_where($this->detail_table, ['head_id' => $head_id])->result_array();
    }
    
    public function delete_kml($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->head_table);
    }
    
    public function update_kml_head($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->head_table, $data);
    }
    
    public function update_kml_detail($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->detail_table, $data);
    }
    
    // Fungsi untuk mengambil data detail berdasarkan ID
    public function get_kml_detail_by_id($id) {
        return $this->db->get_where($this->detail_table, ['id' => $id])->row();
    }
    
    // Fungsi untuk menghapus data detail berdasarkan ID
    public function delete_kml_detail($id) {
        return $this->db->delete($this->detail_table, ['id' => $id]);
    }
}