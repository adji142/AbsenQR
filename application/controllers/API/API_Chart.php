<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Chart extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function AddChart()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$chartData = $this->input->post('chartData');
		$MobileToken = $this->input->post('MobileToken');

		$errorNo = 0;
		$errorMessage = '';

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$chartData = json_decode($chartData);
			// var_dump(count($chartData));
			$this->db->trans_begin();
			try {
				// Cek Trx;

				$cekExist = $this->ModelsExecuteMaster->FindData(array("UserID"=>$chartData[0]->UserID),'chart');
				if ($cekExist->num_rows() > 0) {
					$delete = $this->ModelsExecuteMaster->DeleteData(array("UserID"=>$chartData[0]->UserID),'chart');
					if (!$delete) {
						$undone = $this->db->error();
						$errorNo = -10001;
						$errorMessage = "Failed Exec Statement :" .$undone['message'];

						goto jump;
					}
				}
				for ($i=0; $i < count($chartData) ; $i++) { 
					// echo $chartData[$i]->KodeItem."<br>";
					if ($chartData[$i]->Qty > 0) {
						$param = array(
							'id' => 0,
							'KodeItem' => $chartData[$i]->KodeItem,
							'Qty' => $chartData[$i]->Qty,
							'Harga' => $chartData[$i]->Harga,
							'LineTotal' => $chartData[$i]->LineTotal,
							'KodeSatuan' => $chartData[$i]->KodeSatuan,
							'NamaSatuan' => $chartData[$i]->NamaSatuan,
							'UserID' => $chartData[$i]->UserID,
							'CreatedOn' => date("Y-m-d h:i:sa")
						);

						$rs = $this->ModelsExecuteMaster->ExecInsert($param,'chart');
						if (!$rs) {
							$undone = $this->db->error();
							$errorNo = -10001;
							$errorMessage = "Failed Exec Statement :" .$undone['message'];

							goto jump;
						}
					}
				}
			} catch (Exception $e) {
				$errorNo = -20001;
				$errorMessage = "Failed Exception : ".$e->getMessage();

				goto jump;
			}
		}

		jump:

		if ($errorNo == 0) {
			$this->db->trans_commit();
			$data['success'] = true;
		}
		else{
			$this->db->trans_rollback();
			$data['message'] = "Error: ".$errorNo." - ".$errorMessage;
		}

		echo json_encode($data);
	}

	public function EditCart()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$MobileToken = $this->input->post('MobileToken');
		$KodeItem = $this->input->post('KodeItem');
		$Qty = $this->input->post('Qty');
		$Harga = $this->input->post('Harga');
		$UserID= $this->input->post('UserID');

		$errorNo = 0;
		$errorMessage = '';

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$this->db->trans_begin();
			try {
				if ($Qty == "0") {
					$delCart = $this->ModelsExecuteMaster->DeleteData(array('KodeItem'=>$KodeItem,'UserID'=>$UserID),'chart');
					if (!$delCart) {
						$undone = $this->db->error();
						$errorNo = -10001;
						$errorMessage = "Failed Exec Statement :" .$undone['message'];

						goto jump;
					}
				}
				else{
					$rs = $this->ModelsExecuteMaster->ExecUpdate(array('Qty'=>$Qty,'LineTotal'=>$Qty * $Harga),array('KodeItem'=>$KodeItem,'UserID'=>$UserID),'chart');
					if (!$rs) {
						$undone = $this->db->error();
						$errorNo = -10001;
						$errorMessage = "Failed Exec Statement :" .$undone['message'];

						goto jump;
					}
				}
			} catch (Exception $e) {
				$errorNo = -20001;
				$errorMessage = "Failed Exception : ".$e->getMessage();

				goto jump;
			}
		}

		jump:

		if ($errorNo == 0) {
			$this->db->trans_commit();
			$data['success'] = true;
		}
		else{
			$this->db->trans_rollback();
			$data['message'] = "Error: ".$errorNo." - ".$errorMessage;
		}

		echo json_encode($data);

	}
	public function EditUserChart()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$MobileToken = $this->input->post('MobileToken');
		$DeviceID = $this->input->post('DeviceID');
		$UserID = $this->input->post('UserID');

		$errorNo = 0;
		$errorMessage = '';

		$this->db->trans_begin();

		// var_dump("expression");
		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			try {
				$rs = $this->ModelsExecuteMaster->ExecUpdate(array('UserID'=>$UserID),array('UserID'=>$DeviceID),'chart');

				if (!$rs) {
					$undone = $this->db->error();
					$errorNo = -10001;
					$errorMessage = "Failed Exec Statement :" .$undone['message'];

					goto jump;
				}

			} catch (Exception $e) {
				$errorNo = -20001;
				$errorMessage = "Failed Exception : ".$e->getMessage();

				goto jump;
			}
		}

		jump:

		if ($errorNo == 0) {
			$this->db->trans_commit();
			$data['success'] = true;
		}
		else{
			$this->db->trans_rollback();
			$data['message'] = "Error: ".$errorNo." - ".$errorMessage;
		}

		echo json_encode($data);
	}
	public function getChart()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$UserID = $this->input->post('UserID');
		$KodeItem = $this->input->post('KodeItem');
		$MobileToken = $this->input->post('MobileToken');
		$errorNo = 0;
		$errorMessage = '';

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			try {
				$SQL = "SELECT 
					a.*,
					b.NamaItem,
					b.ImageLink,
					b.KodeSatuan,
					b.NamaSatuan,
					b.KodeKategori,
					b.NamaKategori,
					b.LastPrice,
					b.OldPrice,
					b.OtherPrice,
					b.QtyMinimum
				FROM chart a
				LEFT JOIN itemmasterdata b on a.KodeItem = b.KodeItem
				WHERE 1= 1 ";
				if ($UserID != "") {
					$SQL .= " AND UserID LIKE '%".$UserID."%' ";
				}
				if ($KodeItem !="") {
					$SQL .= " AND KodeItem = '".$KodeItem."' ";
				}
				$rs = $this->db->query($SQL);
				if ($rs->num_rows() > 0) {
					$data['success'] = true;
					$data['data'] = $rs->result();
				}
			} catch (Exception $e) {
				$data['success'] = false;
				$errorNo = -20001;
				$errorMessage = "Failed Exception : ".$e->getMessage();
			}
		}

		echo json_encode($data);
	}
	public function getUserSumChart()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array(),'Total'=>0);

		$UserID = $this->input->post('UserID');
		$MobileToken = $this->input->post('MobileToken');
		$errorNo = 0;
		$errorMessage = '';

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			try {
				$SQL = "SELECT COALESCE(SUM(LineTotal),0) Total FROM chart where 1 = 1 ";
				if ($UserID != "") {
					$SQL .= " AND UserID = '".$UserID."' ";
				}
				$rs = $this->db->query($SQL);
				if ($rs->num_rows() > 0) {
					$data['success'] = true;
					$data['data'] = $rs->result();
					$data['Total'] = $rs->row()->Total;
				}
			} catch (Exception $e) {
				$data['success'] = false;
				$errorNo = -20001;
				$errorMessage = "Failed Exception : ".$e->getMessage();
			}
		}

		echo json_encode($data);
	}
}