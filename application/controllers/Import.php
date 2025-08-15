<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Import extends CI_Controller {
    public function __construct(){ parent::__construct(); $this->load->database(); $this->load->helper(['url','auth']); $this->load->library('session'); require_login(); if(!is_admin()) show_error('Forbidden',403); }
    public function preload(){
        $data_dir = FCPATH . 'public/data/';
        $files = glob($data_dir . '*');
        $count = 0;
        foreach($files as $file){
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if(in_array($ext, ['geojson','json'])){
                $json = file_get_contents($file);
                $obj = json_decode($json, true);
                if(!$obj || !isset($obj['features'])) continue;
                foreach($obj['features'] as $ft){
                    $geom = $ft['geometry'] ?? null;
                    $props = $ft['properties'] ?? [];
                    if(!$geom) continue;
                    $kel = $props['kelurahan'] ?? ($props['NAMOBJ'] ?? ($props['name'] ?? null));
                    $row = ['kelurahan'=>$kel,'kategori'=>null,'name'=>$props['name']??($kel?:null),'color'=>'#3388ff','geometry'=>json_encode($geom, JSON_UNESCAPED_UNICODE),'properties'=>json_encode($props, JSON_UNESCAPED_UNICODE)];
                    $this->db->insert('geojson_data',$row); $count++;
                }
            } elseif($ext=='kml'){
                $xml = simplexml_load_file($file);
                if(!$xml) continue;
                $placemarks = $xml->xpath('//Placemark');
                foreach($placemarks as $pm){
                    $name = (string)($pm->name ?? '');
                    $coords = $pm->xpath('.//coordinates');
                    if(!$coords) continue;
                    foreach($coords as $c){
                        $text = trim((string)$c);
                        if(!$text) continue;
                        $pairs = preg_split('/\s+/', trim($text));
                        $pts = []; foreach($pairs as $p){ $parts = explode(',', trim($p)); if(count($parts)>=2){ $lon=floatval($parts[0]); $lat=floatval($parts[1]); $pts[] = [$lon,$lat]; } }
                        if(count($pts)<1) continue;
                        if(count($pts)>=3){ if($pts[0]!=$pts[count($pts)-1]) $pts[] = $pts[0]; $geom=['type'=>'Polygon','coordinates'=>[$pts]]; } else { $geom=['type'=>'Point','coordinates'=>$pts[0]]; }
                        $row=['kelurahan'=>$name,'kategori'=>null,'name'=>$name,'color'=>'#3388ff','geometry'=>json_encode($geom, JSON_UNESCAPED_UNICODE),'properties'=>json_encode([])];
                        $this->db->insert('geojson_data',$row); $count++;
                    }
                }
            }
        }
        $this->session->set_flashdata('msg','Preload done: ' . $count); redirect('data');
    }
}
