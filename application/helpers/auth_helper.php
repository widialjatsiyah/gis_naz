<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function current_user() { $CI =& get_instance(); return $CI->session->userdata('user'); }
function require_login() { $CI =& get_instance(); if (!$CI->session->userdata('user')) { redirect('login'); exit; } }
function is_admin() { $u = current_user(); return $u && isset($u['role']) && $u['role']==='admin'; }
