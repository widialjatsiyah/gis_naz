<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'auth', 'form']);
        $this->load->library(['session', 'form_validation']);
        $this->load->model('Kml_model');
        require_login();
        check_admin(); // Hanya admin yang bisa mengakses data management
    }

    public function index()
    {
        $this->load->model('Kml_model');
        $this->Kml_model->create_tables_if_not_exists();
        $data['kml_files'] = $this->Kml_model->get_all_head_data();
        $data['kecamatan_list'] = $this->Kml_model->get_all_kecamatan();
        
        $this->load->view('layouts/header', ['user' => current_user()]);
        $this->load->view('data/list', $data);
        $this->load->view('layouts/footer');
    }

    public function list_kml()
    {
        $this->Kml_model->create_tables_if_not_exists();
        $data['kml_files'] = $this->Kml_model->get_all_head_data();
        $data['kecamatan_list'] = $this->Kml_model->get_all_kecamatan();
        
        $this->load->view('layouts/header', ['user' => current_user()]);
        $this->load->view('data/list', $data);
        $this->load->view('layouts/footer');
    }

    public function view_kml($id)
    {
        $this->Kml_model->create_tables_if_not_exists();
        $data['kml_head'] = $this->Kml_model->get_head_by_id($id);
        
        if (!$data['kml_head']) {
            show_404();
        }
        
        $data['kml_details'] = $this->Kml_model->get_detail_data($id);
        
        $this->load->view('layouts/header', ['user' => current_user()]);
        $this->load->view('data/edit', $data);
        $this->load->view('layouts/footer');
    }
    
    public function edit_kml($id)
    {
        // Cek apakah ini request AJAX
        if ($this->input->is_ajax_request()) {
            $kml_head = $this->Kml_model->get_head_by_id($id);
            
            if (!$kml_head) {
                echo json_encode(['status' => 'error', 'message' => 'Data KML tidak ditemukan']);
                return;
            }
            
            $this->form_validation->set_rules('name', 'Nama File KML', 'required|trim');
            $this->form_validation->set_rules('description', 'Deskripsi', 'trim');
            
            if ($this->form_validation->run() == TRUE) {
                $update_data = [
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'kecamatan_id' => $this->input->post('kecamatan_id') ?: null
                ];
                
                $this->Kml_model->update_kml_head($id, $update_data);
                echo json_encode(['status' => 'success', 'message' => 'Data KML berhasil diperbarui']);
            } else {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            }
            return;
        }
        
        // Untuk backward compatibility, tetap support akses langsung
        $data['kml_head'] = $this->Kml_model->get_head_by_id($id);
        $data['kecamatan_list'] = $this->Kml_model->get_all_kecamatan();
        
        if (!$data['kml_head']) {
            show_404();
        }
        
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Nama File KML', 'required|trim');
            $this->form_validation->set_rules('description', 'Deskripsi', 'trim');
            
            if ($this->form_validation->run() == TRUE) {
                $update_data = [
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'kecamatan_id' => $this->input->post('kecamatan_id') ?: null
                ];
                
                $this->Kml_model->update_kml_head($id, $update_data);
                $this->session->set_flashdata('msg', 'Data KML berhasil diperbarui');
                redirect('data/list_kml');
            }
        }
        
        $this->load->view('layouts/header', ['user' => current_user()]);
        $this->load->view('data/edit_head', $data);
        $this->load->view('layouts/footer');
    }
    
    public function delete_kml($id) {
        // Pastikan hanya admin yang bisa menghapus
        if (!is_admin()) {
            show_error('Akses ditolak', 403);
            return;
        }
        
        // Periksa apakah data dengan ID tersebut ada
        $kml_head = $this->Kml_model->get_head_by_id($id);
        if (!$kml_head) {
            show_404();
            return;
        }
        
        // Hapus data (akan menghapus semua detail terkait karena menggunakan CASCADE DELETE)
        $this->Kml_model->delete_head_data($id);
        
        // Set pesan sukses
        $this->session->set_flashdata('msg', 'Data KML dan semua detail terkait berhasil dihapus');
        
        // Redirect ke halaman list
        redirect('data/list_kml');
    }
}