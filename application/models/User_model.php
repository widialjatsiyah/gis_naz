<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_model extends CI_Model { private $table='users'; public function find_by_username($u){ return $this->db->get_where($this->table,['username'=>$u])->row(); } }
