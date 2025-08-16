<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kecamatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'auth', 'form']);
        $this->load->library(['session', 'form_validation']);
        $this->load->model('Kml_model');
        require_login();
        check_admin(); // Hanya admin yang bisa mengakses master kecamatan
    }

    public function index()
    {
        $this->Kml_model->create_tables_if_not_exists();
        $data['kecamatan_list'] = $this->Kml_model->get_all_kecamatan();
        
        $this->load->view('layouts/header', ['user' => current_user()]);
        $this->load->view('kecamatan/list', $data);
        $this->load->view('layouts/footer');
    }

    public function create()
    {
        // Cek apakah ini request AJAX
        $is_ajax = $this->input->is_ajax_request();
        
        if ($this->input->method() === 'post') {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Nama Kecamatan', 'required|trim|is_unique[master_kecamatan.name]');
            $this->form_validation->set_rules('description', 'Deskripsi', 'trim');
            
            if ($this->form_validation->run() == TRUE) {
                $data = [
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description')
                ];
                
                $this->Kml_model->save_kecamatan($data);
                $success_msg = 'Kecamatan berhasil ditambahkan';
                
                if ($is_ajax) {
                    echo json_encode(['status' => 'success', 'message' => $success_msg]);
                    return;
                } else {
                    $this->session->set_flashdata('msg', $success_msg);
                    redirect('kecamatan');
                }
            } else {
                // Ada error validasi
                if ($is_ajax) {
                    $errors = [];
                    if (form_error('name')) {
                        $errors['name'] = strip_tags(form_error('name'));
                    }
                    
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Terjadi kesalahan validasi',
                        'errors' => $errors
                    ]);
                    return;
                }
                // Jika bukan AJAX, biarkan mengikuti alur normal (akan menampilkan form dengan error)
            }
        }
        
        // Untuk request non-AJAX, tampilkan form seperti biasa
        if (!$is_ajax) {
            $this->load->view('layouts/header', ['user' => current_user()]);
            $this->load->view('kecamatan/form');
            $this->load->view('layouts/footer');
        }
    }

    public function edit($id)
    {
        // Cek apakah ini request AJAX
        $is_ajax = $this->input->is_ajax_request();
        
        $data['kecamatan'] = $this->Kml_model->get_kecamatan_by_id($id);
        
        if (!$data['kecamatan']) {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => 'Kecamatan tidak ditemukan']);
                return;
            } else {
                show_404();
            }
        }
        
        if ($this->input->method() === 'post') {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Nama Kecamatan', 'required|trim');
            $this->form_validation->set_rules('description', 'Deskripsi', 'trim');
            
            // Cek jika nama berubah, maka perlu cek unique
            if ($this->input->post('name') !== $data['kecamatan']['name']) {
                $this->form_validation->set_rules('name', 'Nama Kecamatan', 'required|trim|is_unique[master_kecamatan.name]');
            }
            
            if ($this->form_validation->run() == TRUE) {
                $update_data = [
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description')
                ];
                
                $this->Kml_model->update_kecamatan($id, $update_data);
                $success_msg = 'Kecamatan berhasil diperbarui';
                
                if ($is_ajax) {
                    echo json_encode(['status' => 'success', 'message' => $success_msg]);
                    return;
                } else {
                    $this->session->set_flashdata('msg', $success_msg);
                    redirect('kecamatan');
                }
            } else {
                // Ada error validasi
                if ($is_ajax) {
                    $errors = [];
                    if (form_error('name')) {
                        $errors['name'] = strip_tags(form_error('name'));
                    }
                    
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Terjadi kesalahan validasi',
                        'errors' => $errors
                    ]);
                    return;
                }
                // Jika bukan AJAX, biarkan mengikuti alur normal (akan menampilkan form dengan error)
            }
        }
        
        // Untuk request non-AJAX, tampilkan form seperti biasa
        if (!$is_ajax) {
            $this->load->view('layouts/header', ['user' => current_user()]);
            $this->load->view('kecamatan/form', $data);
            $this->load->view('layouts/footer');
        }
    }

    public function delete($id)
    {
        $kecamatan = $this->Kml_model->get_kecamatan_by_id($id);
        
        if (!$kecamatan) {
            show_404();
        }
        
        // Cek apakah kecamatan ini digunakan oleh KML head
        $kml_heads = $this->Kml_model->get_kml_by_kecamatan($id);
        
        if (!empty($kml_heads)) {
            $this->session->set_flashdata('error', 'Tidak dapat menghapus kecamatan yang masih digunakan oleh data KML');
            redirect('kecamatan');
            return;
        }
        
        $this->Kml_model->delete_kecamatan($id);
        $this->session->set_flashdata('msg', 'Kecamatan berhasil dihapus');
        redirect('kecamatan');
    }
}