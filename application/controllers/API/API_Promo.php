<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Promo extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function getPromo()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$id 		= $this->input->post('id');
		if ($id == '') {
			$rs = $this->ModelsExecuteMaster->FindData(array('IsActive'=>1),'tbanner');
		}
		else{
			$rs = $this->ModelsExecuteMaster->FindData(array('ImageLink'=>$id,'IsActive'=>1),'tbanner');
		}

		if ($rs->num_rows()>0) {
			$data['success'] = true;
			$data['data'] = $rs->result();
		}
		else{
			$data['message'] = 'No Record Found';
		}
		echo json_encode($data);
	}
}