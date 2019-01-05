<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '../vendor/autoload.php';  
require APPPATH . '/libraries/REST_Controller.php';
use \Firebase\JWT\JWT;
use Restserver\Libraries\REST_Controller;
    Class JsonWebToken extends REST_Controller {
        private $secretkey = '1a2b3c4d5e';
        
        public function __construct(){
            parent::__construct();
            $this->load->library('form_validation');
        }

        public function generate_post(){
            $this->load->model('M_api'); 
            $date = new DateTime();
            $username = $this->post('username',TRUE); 
            $pass = md5($this->post('password',TRUE)); 
            $dataadmin = $this->M_api->is_valid($username);
            if ($dataadmin) {
                if (password_verify (md5($this->post('password')),password_hash($dataadmin->password, PASSWORD_DEFAULT))) {
                    $payload['id'] = $dataadmin->id_user;
                    $payload['username'] = $dataadmin->username;
                    $payload['iat'] = $date->getTimestamp(); 
                    //$payload['exp'] = $date->getTimestamp() + 3600;  //session expired 1 hour
                    $output['token'] = JWT::encode($payload,$this->secretkey);
                    return $this->response($output,REST_Controller::HTTP_OK);
                } else {
                    $this->viewtokenfail($username);
                }
            } else {
                $this->viewtokenfail($username);
            }
        }

        public function viewtokenfail($username){
            $this->response([
              'status'=>'0',
              'username'=>$username,
              'message'=>'Invalid Username Or Password'
              ],REST_Controller::HTTP_BAD_REQUEST);
        }

        public function cektoken(){
            $this->load->model('M_api'); 
            $jwt = $this->input->get_request_header('Authorization');
            try {
                $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
                if ($this->M_api->is_valid_num($decode->username)>0) {
                    return true;
                }
            } catch (Exception $e) {
                exit(json_encode(array('status' => '0' ,'message' => 'Invalid Token',)));
            }
        }

    }
    ?>