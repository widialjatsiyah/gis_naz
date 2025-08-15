<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'auth']);
        $this->load->library('session');
        $this->load->model('Kml_model');
        require_login();
        check_admin(); // Hanya admin yang bisa mengakses data management
    }

    public function index()
    {
        $this->load->model('Kml_model');
        $this->Kml_model->create_tables_if_not_exists();
        $data['kml_files'] = $this->Kml_model->get_all_head_data();
        
        $this->load->view('layouts/header', ['user' => current_user()]);
        $this->load->view('data/list', $data);
        $this->load->view('layouts/footer');
    }

    public function list_kml()
    {
        $this->Kml_model->create_tables_if_not_exists();
        $data['kml_files'] = $this->Kml_model->get_all_head_data();
        
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