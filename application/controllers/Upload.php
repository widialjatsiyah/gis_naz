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
        // Cek apakah ini request AJAX
        $is_ajax = $this->input->is_ajax_request();
        
        // Dapatkan daftar kecamatan untuk ditampilkan di form
        $data['kecamatan_list'] = $this->Kml_model->get_all_kecamatan();
        
        if ($this->input->method(TRUE) === 'POST' && isset($_FILES['kml'])) {
            $target = FCPATH . 'public/data/uploads/' . time() . '_' . basename($_FILES['kml']['name']);
            if (!move_uploaded_file($_FILES['kml']['tmp_name'], $target)) {
                $error_msg = 'Upload gagal';
                if ($is_ajax) {
                    echo json_encode(['status' => 'error', 'message' => $error_msg]);
                    return;
                } else {
                    $this->session->set_flashdata('msg', $error_msg);
                    redirect('upload/kml');
                }
            }
            
            $xml = @simplexml_load_file($target);
            if (!$xml) {
                $error_msg = 'KML invalid';
                if ($is_ajax) {
                    echo json_encode(['status' => 'error', 'message' => $error_msg]);
                    return;
                } else {
                    $this->session->set_flashdata('msg', $error_msg);
                    redirect('upload/kml');
                }
            }
            
            // Register namespaces
            $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
            
            // Coba berbagai kemungkinan XPath untuk placemarks (termasuk yang di dalam folder)
            $placemarks = $xml->xpath('//Placemark[Polygon]') ?: $xml->xpath('//kml:Placemark[kml:Polygon]');
            
            if (!$placemarks) {
                $error_msg = 'Tidak ditemukan Placemark dengan Polygon dalam file KML';
                if ($is_ajax) {
                    echo json_encode(['status' => 'error', 'message' => $error_msg]);
                    return;
                } else {
                    $this->session->set_flashdata('msg', $error_msg);
                    redirect('upload/kml');
                }
            }
            
            // Dapatkan nama dan deskripsi dokumen KML
            $kml_name = (string)($xml->Document->name ?? $xml->name ?? 'KML File');
            $kml_description = (string)($xml->Document->description ?? $xml->description ?? '');
            
            // Dapatkan kecamatan_id dari form
            $kecamatan_id = $this->input->post('kecamatan_id') ?: null;
            
            // Ekstrak styles dari KML
            $styles = $this->extractStyles($xml);
            
            // Proses placemarks
            $placemark_data = [];
            foreach ($placemarks as $pm) {
                $name = (string)($pm->name ?? '');
                if (empty($name)) {
                    // Coba dapatkan nama dari field lain jika tidak ada name
                    $name = (string)($pm->description ?? '');
                }
                
                // Ekstrak properties
                $properties = [];
                if ($pm->ExtendedData && $pm->ExtendedData->Data) {
                    foreach ($pm->ExtendedData->Data as $data) {
                        $properties[(string)$data['name']] = (string)$data->value;
                    }
                }
                
                // Tambahkan name dan description ke properties
                if (!empty($name)) {
                    $properties['name'] = $name;
                }
                
                if (!empty((string)$pm->description)) {
                    $properties['description'] = (string)$pm->description;
                }
                
                // Ekstrak informasi folder induk
                $parent = $pm->xpath('parent::Folder|parent::kml:Folder');
                if (!empty($parent)) {
                    $folder_name = (string)($parent[0]->name ?? '');
                    if (!empty($folder_name)) {
                        $properties['folder_name'] = $folder_name;
                    }
                }
                
                // Jika tidak menemukan folder langsung, coba cari folder ancestor
                if (!isset($properties['folder_name'])) {
                    $ancestors = $pm->xpath('ancestor::Folder|ancestor::kml:Folder');
                    if (!empty($ancestors)) {
                        // Ambil folder terdekat (yang paling dalam)
                        $closest_folder = end($ancestors);
                        $folder_name = (string)($closest_folder->name ?? '');
                        if (!empty($folder_name)) {
                            $properties['folder_name'] = $folder_name;
                        }
                    }
                }
                
                // Ekstrak kategori dari deskripsi jika tersedia
                if (isset($properties['description'])) {
                    $description = $properties['description'];
                    // Ekstrak ZNT dari deskripsi
                    if (preg_match('/ZNT\s*:\s*(\w+)/i', $description, $matches)) {
                        $properties['extracted_kategori'] = 'ZNT';
                    } 
                    // Ekstrak BLOK dari deskripsi
                    elseif (preg_match('/BLOK\s*:\s*(\w+)/i', $description, $matches)) {
                        $properties['extracted_kategori'] = 'BLOK';
                    }
                    // Ekstrak OBJEK PAJAK dari deskripsi
                    elseif (stripos($description, 'OBJEK PAJAK') !== false) {
                        $properties['extracted_kategori'] = 'OBJEK PAJAK';
                    }
                    // Ekstrak OBJEK dari deskripsi
                    elseif (stripos($description, 'OBJEK') !== false) {
                        $properties['extracted_kategori'] = 'OBJEK';
                    }
                }
                
                // Ekstrak warna dari style
                $color = '#3388ff'; // default color
                if (isset($pm->styleUrl)) {
                    $styleUrl = (string)$pm->styleUrl;
                    // Hapus tanda # di awal jika ada
                    $styleUrl = ltrim($styleUrl, '#');
                    if (isset($styles[$styleUrl])) {
                        $color = $styles[$styleUrl];
                    }
                }
                $properties['color'] = $color;
                
                // Ekstrak geometry (hanya Polygon untuk saat ini)
                $geometry = null;
                if ($pm->Polygon) {
                    $geometry = [
                        'type' => 'Polygon',
                        'coordinates' => []
                    ];
                    
                    // Ekstrak koordinat
                    if ($pm->Polygon->outerBoundaryIs && $pm->Polygon->outerBoundaryIs->LinearRing) {
                        $coords_text = (string)$pm->Polygon->outerBoundaryIs->LinearRing->coordinates;
                        $coords_array = [];
                        
                        // Parse coordinates
                        $coords_lines = preg_split('/\s+/', trim($coords_text));
                        foreach ($coords_lines as $line) {
                            $line = trim($line);
                            if (!empty($line)) {
                                $coords = explode(',', $line);
                                if (count($coords) >= 2) {
                                    $coords_array[] = [(float)$coords[0], (float)$coords[1]];
                                }
                            }
                        }
                        
                        $geometry['coordinates'] = [$coords_array];
                    }
                }
                
                if ($geometry) {
                    $placemark_data[] = [
                        'properties' => $properties,
                        'geometry' => $geometry
                    ];
                }
            }
            
            // Simpan data ke database dengan kecamatan_id
            $this->Kml_model->save_kml_data($kml_name, $kml_description, $placemark_data, $kecamatan_id);
            
            $success_msg = 'KML file berhasil diupload dan diproses';
            if ($is_ajax) {
                echo json_encode(['status' => 'success', 'message' => $success_msg]);
                return;
            } else {
                $this->session->set_flashdata('msg', $success_msg);
                redirect('data/list_kml');
            }
        }
        
        // Jika bukan AJAX request, tampilkan form seperti biasa
        if (!$is_ajax) {
            $this->load->view('layouts/header', ['user' => current_user()]);
            $this->load->view('upload/kml_form', $data);
            $this->load->view('layouts/footer');
        } else {
            // Untuk AJAX request, kirim data form
            echo json_encode(['status' => 'form', 'data' => $data]);
        }
    }
    
    /**
     * Ekstrak styles dari KML untuk digunakan dalam menentukan warna polygon
     */
    private function extractStyles($xml) {
        $styles = [];
        
        // Ekstrak Style elements
        $styleElements = $xml->xpath('//Style') ?: $xml->xpath('//kml:Style');
        foreach ($styleElements as $style) {
            $styleId = (string)$style['id'];
            if (!empty($styleId) && isset($style->PolyStyle->color)) {
                // Konversi warna KML (aabbggrr) ke format web (rrggbb)
                $kmlColor = (string)$style->PolyStyle->color;
                $webColor = $this->kmlColorToWeb($kmlColor);
                $styles[$styleId] = $webColor;
            }
        }
        
        // Ekstrak StyleMap elements
        $styleMapElements = $xml->xpath('//StyleMap') ?: $xml->xpath('//kml:StyleMap');
        foreach ($styleMapElements as $styleMap) {
            $styleMapId = (string)$styleMap['id'];
            if (!empty($styleMapId)) {
                // Cari pasangan dengan key=normal
                foreach ($styleMap->Pair as $pair) {
                    if ((string)$pair->key === 'normal' && isset($pair->styleUrl)) {
                        $styleUrl = ltrim((string)$pair->styleUrl, '#');
                        if (isset($styles[$styleUrl])) {
                            $styles[$styleMapId] = $styles[$styleUrl];
                        }
                    }
                }
            }
        }
        
        return $styles;
    }
    
    /**
     * Konversi warna KML (aabbggrr) ke format web (rrggbb)
     */
    private function kmlColorToWeb($kmlColor) {
        // KML color format: aabbggrr (alpha, blue, green, red)
        // Web color format: rrggbb (red, green, blue)
        if (strlen($kmlColor) == 8) {
            $alpha = substr($kmlColor, 0, 2);
            $blue = substr($kmlColor, 2, 2);
            $green = substr($kmlColor, 4, 2);
            $red = substr($kmlColor, 6, 2);
            
            // Format web: rrggbb
            return '#' . $red . $green . $blue;
        }
        
        // Return default jika format tidak sesuai
        return '#3388ff';
    }
}