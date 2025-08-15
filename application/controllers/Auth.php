<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'um');
        $this->load->helper(['url', 'form', 'auth']);
        $this->load->library('session');
    }
    public function login()
    {
        if ($this->session->userdata('user')) redirect('map');
        if ($this->input->method(TRUE) === 'POST') {
            $u = $this->input->post('username', TRUE);
            $p = $this->input->post('password', TRUE);
            $user = $this->um->find_by_username($u);
            if ($user && password_verify($p, $user->password_hash)) {
                $this->session->set_userdata('user', ['id' => $user->id, 'username' => $user->username, 'role' => $user->role]);
                redirect('map');
            }
            $this->load->view('login_view', ['error' => 'Username atau password salah']);
            return;
        }
        $this->load->view('login_view');
    }
    public function logout()
    {
        $this->session->unset_userdata('user');
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
