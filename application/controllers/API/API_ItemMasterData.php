<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_ItemMasterData extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function getItem()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$errorNo = 0;
		$errorMessage = '';

		$MobileToken = $this->input->post('MobileToken');
		$KodeItem = $this->input->post('KodeItem');
		$Kriteria = $this->input->post('Kriteria');

		$SQL = "";
		try {
			if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
				$SQL .= "SELECT * FROM itemmasterdata where 1 = 1";

				if ($KodeItem != '') {
					$SQL .= " AND KodeItem ='".$KodeItem."' ";
				}

				if ($Kriteria != '') {
					$SQL .= " AND CONCAT(KodeItem,' ',NamaItem, ' ',NamaKategori) LIKE '%".$Kriteria."%'";
				}
				$rs = $this->db->query($SQL);
				
				if ($rs->num_rows() > 0) {
					$data['success'] = true;
					$data['data'] = $rs->result();
				}
			}	
		} catch (Exception $e) {
			$errorNo = -1000;
			$errorMessage = "Exception : " .$e->getMessage();
			goto jump;
		}

		jump:

		echo json_encode($data);
	}
}