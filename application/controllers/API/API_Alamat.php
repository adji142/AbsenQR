<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Alamat extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function ReadAlamat()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$MobileToken = $this->input->post('MobileToken');
		$KodeCustomer = $this->input->post('KodeCustomer');
		$isCheckout = $this->input->post('isCheckout');

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL = "
				SELECT a.*, b.prov_id, c.city_id, d.dis_id kec_id, e.subdis_id kel_id,
				CONCAT(UPPER(a.Kecamatan), ' ', UPPER(a.Kelurahan), ' ', UPPER(a.Kota), ' ', UPPER(a.Provinsi), ' ', UPPER(a.KodePos)) FullAddress
				FROM alamatpelanggan a
				LEFT JOIN dem_provinsi b on a.Provinsi = b.prov_name
				LEFT JOIN dem_kota c on a.Kota = c.city_name AND c.prov_id = b.prov_id
				LEFT JOIN dem_kecamatan d on a.Kecamatan = d.dis_name AND d.city_id = c.city_id
				LEFT JOIN dem_kelurahan e on a.Kelurahan = e.subdis_name AND e.dis_id = d.dis_id
				WHERE a.isActive = 1
			";
			if($KodeCustomer != ""){
				$SQL .= " AND a.KodeCustomer = '".$KodeCustomer."' ";
			}
			if ($isCheckout != "") {
				$SQL .= " AND a.isDefault = 1 ";	
			}

			$rs  =$this->db->query($SQL);
			if ($rs->num_rows() > 0) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
		}
		else{
			$data['message'] = "Invalid Token";
		}

		echo json_encode($data);
	}
	public function CRUD()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());
		$MobileToken = $this->input->post('MobileToken');
		$KodeCustomer = $this->input->post('KodeCustomer');

		$id = $this->input->post('id');
		$Name = $this->input->post('Name');
		$Provinsi = $this->input->post('Provinsi');
		$Kota = $this->input->post('Kota');
		$Kelurahan = $this->input->post('Kelurahan');
		$Kecamatan = $this->input->post('Kecamatan');
		$KodePos = $this->input->post('KodePos');
		$Alamat = $this->input->post('Alamat');
		$Koordinat = $this->input->post('Koordinat');
		$isActive = 1;
		$isDefault = $this->input->post('isDefault');
		$formType = $this->input->post('formType');

		$errorNo = 0;
		$errorMessage = '';
		$this->db->trans_begin();

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {

			$param = array(
				'id' => $id,
				'Name' => $Name,
				'KodeCustomer' => $KodeCustomer,
				'Provinsi' => $Provinsi,
				'Kota' => $Kota,
				'Kelurahan' => $Kelurahan,
				'Kecamatan' => $Kecamatan,
				'KodePos' => $KodePos,
				'Alamat' => $Alamat,
				'Koordinat' => $Koordinat,
				'isActive' => $isActive,
				'isDefault' => $isDefault
			);


			try {
				if ($isDefault == "1") {
					$this->ModelsExecuteMaster->ExecUpdate(array('isDefault'=>0),array('KodeCustomer'=>$KodeCustomer),'alamatpelanggan');
				}

				$rs;
				if ($formType == "add") {
					$rs = $this->ModelsExecuteMaster->ExecInsert($param,'alamatpelanggan');
				}
				elseif ($formType == "edit") {
					$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('KodeCustomer'=>$KodeCustomer,'id'=>$id),'alamatpelanggan');
				}

				if (!$rs) {
					$undone = $this->db->error();

					$errorNo = -1000;
					$errorMessage = "Sistem Gagal Melakukan Pemrosesan Data : " .$undone['message'];
					goto jump;
				}
			} catch (Exception $e) {
				$errorNo = 69;
				$errorMessage = "Exception Runtime Error : ". $e->getMessage();
				goto jump;
			}
		}
		else{
			$errorNo = 403;
			$errorMessage = "UnAutorize Device";
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

	public function getLookup()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$MobileToken = $this->input->post('MobileToken');
		$KodeCustomer = $this->input->post('KodeCustomer');
		$isCheckout = $this->input->post('isCheckout');

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL = "
				SELECT CONCAT(Name,'|',Koordinat) ID,  CONCAT(UPPER(a.Kecamatan), ' ', UPPER(a.Kelurahan), ' ', UPPER(a.Kota), ' ', UPPER(a.Provinsi), ' ', UPPER(a.KodePos)) Subtitle, CONCAT(b.NamaCustomer , ' [', a.Name , ']') Title
				FROM alamatpelanggan a
				LEFT JOIN masterpelanggan b on a.KodeCustomer = b.KodeCustomer
				WHERE a.isActive = 1
			";
			if($KodeCustomer != ""){
				$SQL .= " AND a.KodeCustomer = '".$KodeCustomer."' ";
			}
			
			$rs  =$this->db->query($SQL);
			if ($rs->num_rows() > 0) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
		}
		else{
			$data['message'] = "Invalid Token";
		}

		echo json_encode($data["data"]);
	}
}