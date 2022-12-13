<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Token extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function SetToken()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$errorNo = 0;
		$errorMessage = '';

		$DeviceID = $this->input->post('DeviceID');
		$GeneratedDate = date("Y-m-d h:i:sa");

		$param = array(
			'DeviceID' => $DeviceID,
			'Token' => base64_encode($DeviceID),
			'GeneratedDate' => $GeneratedDate,
		);

		try {
			$Query = $this->ModelsExecuteMaster->FindData(array('DeviceID'=>$DeviceID),'tokenpools');
			if ($Query->num_rows() == 0) {
				$rs = $this->ModelsExecuteMaster->ExecInsert($param,'tokenpools');
				if (!$rs) {
					$undone = $this->db->error();

					$errorNo = -1001;
					$errorMessage = 'Gagal Generate Token : '.$undone['message'];;
					goto jump;
				}
			}
		} catch (Exception $e) {
			$errorNo = -69;
			$errorMessage = $e->getMessage();
			goto jump;
		}
		jump:
		if ($errorNo == 0) {
			$data['success'] = true;
		}
		else{
			$data['success'] = false;
			$data['message'] = $errorMessage;
		}

		echo json_encode($data);
	}
	
	public function GetTokenAuth()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$token = $this->input->post('token');
		$data['success'] = $this->ModelsExecuteMaster->GetToken($token);

		echo json_encode($data);
	}

}