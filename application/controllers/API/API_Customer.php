<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Customer extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function NewCustomerFromApps()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$errorNo = 0;
		$errorMessage = '';

		$KodeCustomer = $this->input->post('KodeCustomer');
		$NamaCustomer = $this->input->post('NamaCustomer');
		$KodeTermin = $this->input->post('KodeTermin');
		$NamaTermin = $this->input->post('NamaTermin');
		$NoTlp = $this->input->post('NoTlp');
		$NoTlp2 = $this->input->post('NoTlp2');
		$Email = $this->input->post('Email');
		$Provinsi = $this->input->post('Provinsi');
		$Kota = $this->input->post('Kota');
		$Kelurahan = $this->input->post('Kelurahan');
		$Kecamatan = $this->input->post('Kecamatan');
		$KodePos = $this->input->post('KodePos');
		$AddressCode = '';
		$Alamat = $this->input->post('Alamat');
		$Koordinat = $this->input->post('Koordinat');
		$ContactPerson = $this->input->post('ContactPerson');
		$NamaInstansi = $this->input->post('NamaInstansi');
		$SaldoPiutang = 0;
		$isMitra = 0;
		$isActive = 1;
		$Verified = 0;
		$VerifiedBy = '';
		$VerifiedOn = NULL;
		$PotonganPersen = 0;
		$PotonganRupiah = 0;
		$TempToken = $this->input->post('TempToken');
		$formType = $this->input->post('formType');

		$CreatedOn = date("Y/m/d hh:mm:ss");

		$MobileToken = $this->input->post('MobileToken');
		$this->db->trans_begin();

		// Generate New KodeCustomer

		$Kolom = 'KodeCustomer';
		$Table = 'masterpelanggan';
		$Prefix = 'CL';

		$SQL = "SELECT RIGHT(MAX(".$Kolom."),5)  AS Total FROM " . $Table . " WHERE LEFT(" . $Kolom . ", LENGTH('".$Prefix."')) = '".$Prefix."'";

		// var_dump($SQL);
		$rs = $this->db->query($SQL);

		$temp = $rs->row()->Total + 1;

		$nomor = $Prefix.str_pad($temp, 5,"0",STR_PAD_LEFT);
		if ($nomor != '' && $KodeCustomer == '') {
			$KodeCustomer = $nomor;
		}
		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			try {
				$param = array();
				if ($formType == 'add') {
					$param = array(
						'KodeCustomer' => $KodeCustomer,
						'NamaCustomer' => $NamaCustomer,
						'KodeTermin' => $KodeTermin,
						'NamaTermin' => $NamaTermin,
						'NoTlp' => $NoTlp,
						'NoTlp2' => $NoTlp2,
						'Email' => $Email,
						'Provinsi' => $Provinsi,
						'Kota' => $Kota,
						'Kelurahan' => $Kelurahan,
						'Kecamatan' => $Kecamatan,
						'KodePos' => $KodePos,
						'AddressCode' => $AddressCode,
						'Alamat' => $Alamat,
						'Koordinat' => $Koordinat,
						'ContactPerson' => $ContactPerson,
						'NamaInstansi' => $NamaInstansi,
						'SaldoPiutang' => $SaldoPiutang,
						'isMitra' => $isMitra,
						'isActive' => $isActive,
						'Verified' => $Verified,
						'VerifiedBy' => $VerifiedBy,
						'VerifiedOn' => $VerifiedOn,
						'PotonganPersen' => $PotonganPersen,
						'PotonganRupiah' => $PotonganRupiah,
						'TempToken' => $this->encryption->encrypt($TempToken),
						'CreatedOn' => $CreatedOn,
					);
				}
				elseif ($formType == "edit") {
					$param = array(
						'KodeCustomer' => $KodeCustomer,
						'NamaCustomer' => $NamaCustomer,
						'KodeTermin' => $KodeTermin,
						'NamaTermin' => $NamaTermin,
						'NoTlp' => $NoTlp,
						'NoTlp2' => $NoTlp2,
						'Email' => $Email,
						'Provinsi' => $Provinsi,
						'Kota' => $Kota,
						'Kelurahan' => $Kelurahan,
						'Kecamatan' => $Kecamatan,
						'KodePos' => $KodePos,
						'AddressCode' => $AddressCode,
						'Alamat' => $Alamat,
						'Koordinat' => $Koordinat,
						'ContactPerson' => $ContactPerson,
						'NamaInstansi' => $NamaInstansi
					);
				}
				$rs;
				if ($formType == "add") {
					$rs = $this->ModelsExecuteMaster->ExecInsert($param,'masterpelanggan');
				}
				elseif ($formType == "edit") {
					$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('KodeCustomer'=>$KodeCustomer),'masterpelanggan');
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
	public function ReadCustomer()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$KodeCustomer = $this->input->post('KodeCustomer');
		$MobileToken = $this->input->post('MobileToken');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL = "SELECT a.*, b.prov_id, c.city_id, d.dis_id kec_id, e.subdis_id kel_id, f.Hari,f.Toleransi FROM masterpelanggan a
				LEFT JOIN dem_provinsi b on a.Provinsi = b.prov_name
				LEFT JOIN dem_kota c on a.Kota = c.city_name AND c.prov_id = b.prov_id
				LEFT JOIN dem_kecamatan d on a.Kecamatan = d.dis_name AND d.city_id = c.city_id
				LEFT JOIN dem_kelurahan e on a.Kelurahan = e.subdis_name AND e.dis_id = d.dis_id 
				LEFT JOIN ttermin f on a.KodeTermin = f.id
				WHERE 1 = 1 ";
			if ($KodeCustomer != "") {
				$SQL .= " AND a.KodeCustomer = '".$KodeCustomer."' ";
			}
			if ($phone != "") {
				$SQL .= " AND a.NoTlp = '".$phone."' ";
			}
			if ($email != "") {
				$SQL .= " AND a.Email = '".$email."' ";
			}
			$rs = $this->db->query($SQL);
			if ($rs->num_rows() > 0) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
			else{
				$data['message'] = "No Matching Record Found";
			}
		}
		else{
			$data['message'] = "Error: 403 - UnAutorize Device";
		}

		echo json_encode($data);
	}
	public function ReadDetailCustomer()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$KodeCustomer = $this->input->post('KodeCustomer');
		$MobileToken = $this->input->post('MobileToken');

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL = "
				SELECT 
					a.*,
					CONCAT(UPPER(b.Kecamatan), ' ', UPPER(b.Kelurahan), ' ', UPPER(b.Kota), ' ', UPPER(b.Provinsi), ' ', UPPER(b.KodePos)) FullAddress,
					b.`Name`,
					b.Koordinat defKoordinat
				FROM masterpelanggan a
				LEFT JOIN alamatpelanggan b on a.KodeCustomer = b.KodeCustomer
				WHERE b.isDefault = 1
				AND a.KodeCustomer = '$KodeCustomer'
			";

			$rs = $this->db->query($SQL);
			if ($rs->num_rows() > 0) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
			else{
				$data['message'] = "No Matching Record Found";
			}
		}
		else{
			$data['message'] = "Error: 403 - UnAutorize Device";
		}

		echo json_encode($data);
	}
}