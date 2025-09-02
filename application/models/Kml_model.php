<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kml_model extends CI_Model {
    private $head_table = 'kml_data_head';
    private $detail_table = 'kml_data_detail';
    private $kecamatan_table = 'master_kecamatan';
    
    public function create_tables_if_not_exists() {
        // Membuat tabel master kecamatan
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->kecamatan_table}` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `description` text,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");

        // Membuat tabel head
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->head_table}` (
              `id` int NOT NULL AUTO_INCREMENT,
              `kecamatan_id` int DEFAULT NULL,
              `name` varchar(255) DEFAULT NULL,
              `description` text,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `kecamatan_id` (`kecamatan_id`),
              CONSTRAINT `fk_kecamatan` FOREIGN KEY (`kecamatan_id`) REFERENCES `{$this->kecamatan_table}` (`id`) ON DELETE SET NULL
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
    
    public function save_kml_data($name, $description, $features, $kecamatan_id = null) {
        // Simpan data head
        $head_data = [
            'kecamatan_id' => $kecamatan_id,
            'name' => $name,
            'description' => $description
        ];
        
        $this->db->insert($this->head_table, $head_data);
        $head_id = $this->db->insert_id();
        
        // Simpan data detail
        foreach ($features as $feature) {
            // Ekstrak kategori dari struktur folder KML (BLOK, ZNT, OBJEK PAJAK)
            $kategori = '';
            
            // Cek apakah ada informasi folder dalam properties
            if (isset($feature['properties']['folder_name'])) {
                $folder_name = $feature['properties']['folder_name'];
                // Cek apakah nama folder mengandung kata kunci yang diinginkan
                if (stripos($folder_name, 'BLOK') !== false) {
                    $kategori = 'BLOK';
                } elseif (stripos($folder_name, 'ZNT') !== false) {
                    $kategori = 'ZNT';
                } elseif (stripos($folder_name, 'OBJEK PAJAK') !== false) {
                    $kategori = 'OBJEK PAJAK';
                } elseif (stripos($folder_name, 'OBJEK') !== false && stripos($folder_name, 'OBJEK PAJAK') === false) {
                    $kategori = 'OBJEK';
                }
            }
            
            // Jika tidak ditemukan dari nama folder, cek dari nama polygon itu sendiri
            if (empty($kategori) && isset($feature['properties']['name'])) {
                $polygon_name = $feature['properties']['name'];
                // Cek apakah nama polygon adalah "ZONA NILAI TANAH"
                if (strtoupper(trim($polygon_name)) === 'ZONA NILAI TANAH') {
                    $kategori = 'ZNT';
                }
            }
            
            // Jika tidak ditemukan dari nama folder atau nama polygon, cek dari deskripsi
            if (empty($kategori) && isset($feature['properties']['extracted_kategori'])) {
                $kategori = $feature['properties']['extracted_kategori'];
            }
            
            // Jika tidak ditemukan dari nama folder, nama polygon, atau deskripsi, fallback ke cara lama
            if (empty($kategori)) {
                if (isset($feature['properties']['TIPEHAK'])) {
                    $kategori = $feature['properties']['TIPEHAK'];
                } else if (isset($feature['properties']['kategori'])) {
                    $kategori = $feature['properties']['kategori'];
                }
            }
            
            $detail_data = [
                'head_id' => $head_id,
                'kelurahan' => isset($feature['properties']['kelurahan']) ? $feature['properties']['kelurahan'] : '',
                'kategori' => $kategori,
                'name' => isset($feature['properties']['name']) ? $feature['properties']['name'] : '',
                'color' => isset($feature['properties']['color']) ? $feature['properties']['color'] : '#3388ff',
                'properties' => json_encode($feature['properties']),
                'geometry' => json_encode($feature['geometry'])
            ];
            
            $this->db->insert($this->detail_table, $detail_data);
        }
        
        return $head_id;
    }
    
    public function get_all_head_data() {
        $this->db->select("{$this->head_table}.*, {$this->kecamatan_table}.name as kecamatan_name");
        $this->db->from($this->head_table);
        $this->db->join($this->kecamatan_table, "{$this->head_table}.kecamatan_id = {$this->kecamatan_table}.id", 'left');
        $this->db->order_by("{$this->kecamatan_table}.name", 'ASC');
        $this->db->order_by("{$this->head_table}.name", 'ASC');
        return $this->db->get()->result_array();
    }
    
    public function get_head_by_id($id) {
        $this->db->select("{$this->head_table}.*, {$this->kecamatan_table}.name as kecamatan_name");
        $this->db->from($this->head_table);
        $this->db->join($this->kecamatan_table, "{$this->head_table}.kecamatan_id = {$this->kecamatan_table}.id", 'left');
        $this->db->where("{$this->head_table}.id", $id);
        return $this->db->get()->row_array();
    }
    
    public function get_detail_data($head_id) {
        return $this->db->get_where($this->detail_table, ['head_id' => $head_id])->result_array();
    }
    
    public function delete_head_data($id) {
        // Karena sudah menggunakan CONSTRAINT ON DELETE CASCADE, 
        // menghapus head akan otomatis menghapus semua detail terkait
        return $this->db->delete($this->head_table, ['id' => $id]);
    }
    
    public function update_kml_head($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->head_table, $data);
    }
    
    // Fungsi untuk menyimpan detail data KML (digunakan saat menyimpan polygon yang digambar)
    public function save_kml_detail($data) {
        return $this->db->insert($this->detail_table, $data);
    }
    
    // Fungsi untuk mendapatkan detail data KML berdasarkan ID
    public function get_kml_detail_by_id($id) {
        return $this->db->get_where($this->detail_table, ['id' => $id])->row();
    }
    
    // Fungsi untuk mengupdate detail data KML
    public function update_kml_detail($id, $data) {
        return $this->db->where('id', $id)->update($this->detail_table, $data);
    }
    
    // Fungsi untuk menghapus detail data KML
    public function delete_kml_detail($id) {
        return $this->db->delete($this->detail_table, ['id' => $id]);
    }
    
    // Fungsi untuk mengelola data kecamatan
    public function get_all_kecamatan() {
        $this->db->order_by('name', 'ASC');
        return $this->db->get($this->kecamatan_table)->result_array();
    }
    
    public function get_kecamatan_by_id($id) {
        return $this->db->get_where($this->kecamatan_table, ['id' => $id])->row_array();
    }
    
    public function save_kecamatan($data) {
        $this->db->insert($this->kecamatan_table, $data);
        return $this->db->insert_id();
    }
    
    public function update_kecamatan($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->kecamatan_table, $data);
    }
    
    // Fungsi untuk mendapatkan daftar tipe poligon yang tersedia dalam file KML
    public function get_polygon_types($head_id) {
        $this->db->select("DISTINCT JSON_EXTRACT(properties, '$.TIPEHAK') as tipehak", false);
        $this->db->from($this->detail_table);
        $this->db->where('head_id', $head_id);
        $this->db->where("JSON_EXTRACT(properties, '$.TIPEHAK') IS NOT NULL");
        $query = $this->db->get();
        
        $types = [];
        foreach ($query->result() as $row) {
            // Membersihkan hasil dari tanda kutip jika ada
            $tipe = trim($row->tipehak, '"');
            if (!empty($tipe)) {
                $types[] = $tipe;
            }
        }
        
        return $types;
    }
    
    // Fungsi untuk mendapatkan data detail berdasarkan tipe poligon
    public function get_detail_data_by_types($head_id, $types = []) {
        $this->db->from($this->detail_table);
        $this->db->where('head_id', $head_id);
        
        if (!empty($types)) {
            $this->db->group_start();
            foreach ($types as $type) {
                $this->db->or_where("JSON_EXTRACT(properties, '$.TIPEHAK')", $type);
            }
            $this->db->group_end();
        }
        
        return $this->db->get()->result_array();
    }
    
    // Fungsi untuk mendapatkan daftar kategori poligon yang tersedia dalam file KML
    public function get_polygon_categories($head_id) {
        $categories = [];
        
        // Dapatkan kategori dari field kategori
        $this->db->select("DISTINCT (kategori)");
        $this->db->from($this->detail_table);
        $this->db->where('head_id', $head_id);
        $this->db->where("kategori IS NOT NULL");
        $this->db->where("kategori != ''");
        $query = $this->db->get();
        
        foreach ($query->result() as $row) {
            if (!empty($row->kategori)) {
                $categories[] = $row->kategori;
            }
        }
        
        // Dapatkan kategori dari field TIPEHAK dalam properties
        $this->db->select("DISTINCT JSON_EXTRACT(properties, '$.TIPEHAK') as tipehak", false);
        $this->db->from($this->detail_table);
        $this->db->where('head_id', $head_id);
        $this->db->where("JSON_EXTRACT(properties, '$.TIPEHAK') IS NOT NULL");
        $query = $this->db->get();
        
        foreach ($query->result() as $row) {
            // Bersihkan hasil dari tanda kutip jika ada
            $tipe = trim($row->tipehak, '"');
            if (!empty($tipe)) {
                $categories[] = $tipe;
            }
        }
        
        // Hapus duplikat dan kembalikan array unik
        return array_unique($categories);
    }
    
    // Fungsi untuk mendapatkan data detail berdasarkan kategori poligon
    public function get_detail_data_by_categories($head_id, $categories = []) {
        $this->db->from($this->detail_table);
        $this->db->where('head_id', $head_id);
        
        if (!empty($categories)) {
            $this->db->group_start();
            foreach ($categories as $category) {
                $this->db->or_where("kategori", $category);
            }
            $this->db->group_end();
        }
        
        return $this->db->get()->result_array();
    }
}