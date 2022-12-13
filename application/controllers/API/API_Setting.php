<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Setting extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
		$this->load->model('LoginMod');
	}

	public function ValidateCutof()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$SettingID = $this->input->post('SettingID');
		$SQL = "
			SELECT SettingID, CAST(CONCAT(DATE(NOW()),' ',SettingValue,':00') AS DATETIME), SettingValue FROM appsetting
			WHERE NOW() < CAST(CONCAT(DATE(NOW()),' ',SettingValue,':00') AS DATETIME)
			AND SettingID = '$SettingID'
		";
		$jam = $this->ModelsExecuteMaster->FindData(array('SettingID'=>$SettingID),'appsetting')->row();
		$rs = $this->db->query($SQL);
		if ($rs->num_rows() > 0) {
			$data['success'] = true;
		}
		else{
			$data['success'] = true;
			$data['message'] = "Checkout hanya bisa dilakukan sebelum jam " . $jam->SettingValue;
		}
		echo json_encode($data);
	}
}