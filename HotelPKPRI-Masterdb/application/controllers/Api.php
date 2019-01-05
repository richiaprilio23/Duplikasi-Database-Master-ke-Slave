<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'controllers/JsonWebToken.php';

class Api extends JsonWebToken
{
	function __construct($config = 'rest') {
            parent::__construct($config);
            $this->load->database(); 
            $this->cektoken();
        } 

    function index_get() { 
        $id = $this->get('t_detail_id');
        if ($id == '') {
            $crud = $this->db->get('transaksi_detail')->result();
        } else { 
            $this->db->where('t_detail_id', $id);
            $crud = $this->db->get('transaksi_detail')->result();
        }
        $this->response($crud, 200);
    }

}

?>