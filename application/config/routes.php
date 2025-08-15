<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Default route
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Custom routes
$route['map'] = 'map/index';
$route['data'] = 'data/list_kml';
$route['data/list_kml'] = 'data/list_kml';
$route['data/view_kml/(:num)'] = 'data/view_kml/$1';
$route['upload/kml'] = 'upload/kml';
