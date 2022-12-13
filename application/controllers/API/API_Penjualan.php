<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Penjualan extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function CreateTransaction()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());
		$MobileToken = $this->input->post('MobileToken');

		// Header
		$NoTransaksi = $this->input->post('NoTransaksi');
		$TglTransaksi = $this->input->post('TglTransaksi');
		$TglJatuhTempo = $this->input->post('TglJatuhTempo');
		$KodeCustomer = $this->input->post('KodeCustomer');
		$KodeTermin = $this->input->post('KodeTermin');
		$NamaTermin = $this->input->post('NamaTermin');
		$AlamatPengiriman = $this->input->post('AlamatPengiriman');
		$Koordinat = $this->input->post('Koordinat');
		$ContactPerson = $this->input->post('ContactPerson');
		$NoTlp = $this->input->post('NoTlp');
		$SumSys = $this->input->post('SumSys');
		$OngkosKirim = $this->input->post('OngkosKirim');
		$BiayaLayanan = $this->input->post('BiayaLayanan');
		$Printed = 0;
		$CreatedBy = $this->input->post('CreatedBy');
		$CreatedOn = date("Y-m-d h:i:sa");
		$StatusDocument = $this->input->post('StatusDocument');
		$PaymentTypeCode = $this->input->post('PaymentTypeCode');
		$PaymentTypeName = $this->input->post('PaymentTypeName');

		// Detail
		$detailData = $this->input->post('detailData');

		$errorNo = 0;
		$errorMessage = '';
		$this->db->trans_begin();
		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			try {
				// Generate No Transaksi
				$Kolom = 'NoTransaksi';
				$Table = 'penjualanheader';
				$Prefix = strval(date("Y")).strval(date("m"));

				$SQL = "SELECT RIGHT(MAX(".$Kolom."),6)  AS Total FROM " . $Table . " WHERE LEFT(" . $Kolom . ", LENGTH('".$Prefix."')) = '".$Prefix."'";

				// var_dump($SQL);
				$rs = $this->db->query($SQL);

				$temp = $rs->row()->Total + 1;

				$NoTransaksi = $Prefix.str_pad($temp, 10,"0",STR_PAD_LEFT);

				$paramHeader = array(
					'NoTransaksi' => $NoTransaksi,
					'TglTransaksi' => $TglTransaksi,
					'TglJatuhTempo' => $TglJatuhTempo,
					'KodeCustomer' => $KodeCustomer,
					'KodeTermin' => $KodeTermin,
					'NamaTermin' => $NamaTermin,
					'AlamatPengiriman' => $AlamatPengiriman,
					'Koordinat' => $Koordinat,
					'ContactPerson' => $ContactPerson,
					'NoTlp' => $NoTlp,
					'SumSys' => $SumSys,
					'OngkosKirim' => $OngkosKirim,
					'BiayaLayanan' => $BiayaLayanan,
					'Printed' => $Printed,
					'CreatedBy' => $CreatedBy,
					'CreatedOn' => $CreatedOn,
					'StatusDocument' => $StatusDocument,
					'PaymentTypeCode' => $PaymentTypeCode,
					'PaymentTypeName' => $PaymentTypeName
				);

				$removeChart = $this->ModelsExecuteMaster->DeleteData(array('UserID'=>$CreatedBy), 'chart');

				$header = $this->ModelsExecuteMaster->ExecInsert($paramHeader,'penjualanheader');
				if ($header) {
					$listDetail = json_decode($detailData);
					// var_dump($listDetail);
					for ($i=0; $i < count($listDetail) ; $i++) { 
						$Detail = array(
							'NoTransaksi' 	=> $NoTransaksi,
							'KodeItem' 		=> $listDetail[$i]->KodeItem,
							'NamaItem' 		=> $listDetail[$i]->NamaItem,
							'KodeSatuan' 	=> $listDetail[$i]->KodeSatuan,
							'NamaSatuan'	=> $listDetail[$i]->NamaSatuan,
							'Qty'			=> $listDetail[$i]->Qty,
							'OpenQty'		=> 0,
							'CanceledQty'	=> 0,
							'Harga'			=> $listDetail[$i]->Harga,
							'LineTotal'		=> $listDetail[$i]->LineTotal,
							'Disc' 			=> 0,
						);
						$dataDetail = $this->ModelsExecuteMaster->ExecInsert($Detail, 'penjualandetail');
						if (!$dataDetail) {
							$undone = $this->db->error();
							$errorNo = -10001;
							$errorMessage = "Failed Exec Statement :" .$undone['message'];

							goto jump;
						}
					}

				}
				else{
					$undone = $this->db->error();

					$errorNo = -1000;
					$errorMessage = "Sistem Gagal Melakukan Pemrosesan Data : " .$undone['message'];
					goto jump;
				}
			} catch (Exception $e) {
				$errorNo = -20001;
				$errorMessage = "Failed Exception : ".$e->getMessage();

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

	public function GetTransactionHistory()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());
		$MobileToken = $this->input->post('MobileToken');

		$KodeCustomer = $this->input->post('KodeCustomer');

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL = "
				SELECT 
					a.NoTransaksi,
					Date(a.TglTransaksi) TglTransaksi,
					a.TglJatuhTempo,
					a.StatusDocument,
					a.SumSys,
					COUNT(*) JmlItem
				FROM penjualanheader a
				LEFT JOIN penjualandetail b on a.NoTransaksi = b.NoTransaksi
				WHERE a.KodeCustomer = '$KodeCustomer'
				GROUP BY a.NoTransaksi
			";
			$rs = $this->db->query($SQL);

			if ($rs->num_rows() > 0) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
		}

		echo json_encode($data);
	}
}