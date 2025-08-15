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
}