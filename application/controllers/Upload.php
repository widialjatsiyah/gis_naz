<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'auth']);
        $this->load->library('session');
        $this->load->model('Kml_model');
        require_login();
        if (!is_admin()) show_error('Forbidden', 403);
    }

    public function kml()
    {
        if ($this->input->method(TRUE) === 'POST' && isset($_FILES['kml'])) {
            $target = FCPATH . 'public/data/uploads/' . time() . '_' . basename($_FILES['kml']['name']);
            if (!move_uploaded_file($_FILES['kml']['tmp_name'], $target)) {
                $this->session->set_flashdata('msg', 'Upload gagal');
                redirect('data');
            }
            
            $xml = @simplexml_load_file($target);
            if (!$xml) {
                $this->session->set_flashdata('msg', 'KML invalid');
                redirect('data');
            }
            
            // Register namespaces
            $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
            
            // Coba berbagai kemungkinan XPath untuk placemarks
            $placemarks = $xml->xpath('//Placemark') ?: $xml->xpath('//kml:Placemark');
            
            if (!$placemarks) {
                $this->session->set_flashdata('msg', 'Tidak ditemukan Placemark dalam file KML');
                redirect('data');
            }
            
            // Dapatkan nama dan deskripsi dokumen KML
            $kml_name = (string)($xml->Document->name ?? $xml->name ?? 'KML File');
            $kml_description = (string)($xml->Document->description ?? $xml->description ?? '');
            
            // Proses placemarks
            $placemark_data = [];
            foreach ($placemarks as $pm) {
                $name = (string)($pm->name ?? '');
                if (empty($name)) {
                    // Coba dapatkan nama dari field lain jika tidak ada name
                    $extended_data = $pm->xpath('.//Data[@name="KELURAHAN"]') ?: 
                                   $pm->xpath('.//kml:Data[@name="KELURAHAN"]');
                    if ($extended_data && isset($extended_data[0]['value'])) {
                        $name = (string)$extended_data[0]['value'];
                    } else {
                        $name = (string)($pm->ExtendedData->Data->value ?? '');
                    }
                }
                
                // Cari coordinates dengan berbagai kemungkinan struktur
                $coords = $pm->xpath('.//coordinates') ?: 
                         $pm->xpath('.//kml:coordinates') ?: 
                         $pm->Polygon->outerBoundaryIs->LinearRing->coordinates ?:
                         $pm->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates;
                
                if (!$coords) continue;
                
                foreach ($coords as $c) {
                    $text = trim((string)$c);
                    if (!$text) continue;
                    
                    $pairs = preg_split('/\s+/', trim($text));
                    $pts = [];
                    foreach ($pairs as $p) {
                        $parts = explode(',', trim($p));
                        if (count($parts) >= 2) {
                            $lon = floatval($parts[0]);
                            $lat = floatval($parts[1]);
                            $pts[] = [$lon, $lat];
                        }
                    }
                    
                    if (count($pts) < 3) continue;
                    
                    // Pastikan polygon tertutup
                    if ($pts[0] != $pts[count($pts) - 1]) $pts[] = $pts[0];
                    
                    $geom = ['type' => 'Polygon', 'coordinates' => [$pts]];
                    
                    // Siapkan properti tambahan
                    $properties = [];
                    if (isset($pm->description)) {
                        $properties['description'] = (string)$pm->description;
                    }
                    
                    // Tambahkan data extended ke properties
                    $extended_data_elements = $pm->xpath('.//Data') ?: $pm->xpath('.//kml:Data');
                    if ($extended_data_elements) {
                        foreach ($extended_data_elements as $data) {
                            $attr_name = (string)($data['name'] ?? '');
                            if ($attr_name) {
                                $properties[$attr_name] = (string)($data->value ?? '');
                            }
                        }
                    }
                    
                    $placemark_data[] = [
                        'kelurahan' => $name, 
                        'kategori' => null, 
                        'name' => $name, 
                        'color' => '#3388ff', 
                        'geometry' => json_encode($geom, JSON_UNESCAPED_UNICODE), 
                        'properties' => json_encode($properties)
                    ];
                }
            }
            
            if (empty($placemark_data)) {
                $this->session->set_flashdata('msg', 'Tidak ada data yang dapat diimpor dari file KML');
                redirect('data');
            }
            
            // Simpan data dengan struktur head-detail
            $this->Kml_model->create_tables_if_not_exists();
            $head_id = $this->Kml_model->save_kml_data($kml_name, $kml_description, $placemark_data);
            
            $this->session->set_flashdata('msg', 'Berhasil mengimpor ' . count($placemark_data) . ' fitur dari KML dengan ID: ' . $head_id);
            redirect('data/list_kml');
        }
        
        $this->load->view('layouts/header', ['user' => current_user()]);
        $this->load->view('upload/kml_form');
        $this->load->view('layouts/footer');
    }
}