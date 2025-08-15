<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Export extends CI_Controller {
    public function __construct(){ parent::__construct(); $this->load->database(); $this->load->helper(['url','auth']); $this->load->library('session'); require_login(); }
    public function geojson(){ $kel=$this->input->get('kelurahan',TRUE); if($kel) $this->db->where('kelurahan',$kel); $rows=$this->db->get('geojson_data')->result(); $features=[]; foreach($rows as $r){ $features[]=['type'=>'Feature','properties'=> array_merge(['id'=>$r->id,'kelurahan'=>$r->kelurahan,'name'=>$r->name,'color'=>$r->color], (array)json_decode($r->properties,true)),'geometry'=> json_decode($r->geometry,true)]; } $fc=['type'=>'FeatureCollection','features'=>$features]; header('Content-Type: application/geo+json; charset=utf-8'); header('Content-Disposition: attachment; filename="export.geojson"'); echo json_encode($fc, JSON_UNESCAPED_UNICODE); }
}
