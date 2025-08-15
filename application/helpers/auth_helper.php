<?php
defined('BASEPATH') or exit('No direct script access allowed');
function current_user()
{
    $CI = &get_instance();
    return $CI->session->userdata('user');
}
function require_login()
{
    $CI = &get_instance();
    if (!$CI->session->userdata('user')) {
        redirect('auth/login');
        exit;
    }
}
function is_admin()
{
    $u = current_user();
    return $u && isset($u['role']) && $u['role'] === 'admin';
}

function check_admin()
{
    if (!is_admin()) {
        show_error('Akses ditolak. Hanya administrator yang dapat mengakses halaman ini.', 403);
        exit;
    }
}