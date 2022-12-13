<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Kategori extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function GetKategori()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$MobileToken = $this->input->post('MobileToken');

		$id = $this->input->post('id');
		$rs;

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL = "SELECT * FROM tkategori where 1 = 1";
			if ($id != '') {
				$SQL .= " AND id = ".$id." ";
			}
			$rs = $this->db->query($SQL);

			if ($rs) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
			else{
				$undone = $this->db->error();
				$data['message'] = 'Gagal Melakukan Pemrosesan data : ' . $undone['message'];
			}
		}
		else{
			$data['message'] = 'Invalid Token';
		}

		echo json_encode($data);
	}

	public function GetAppSetting()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$Key = $this->input->post('Key');

		$rs = $this->ModelsExecuteMaster->FindData(array('AppKey'=> $Key),'appsetting');
		if ($rs) {
			$data['success'] = true;
			$data['data'] = $rs->result();
		}
		echo json_encode($data);
	}
}