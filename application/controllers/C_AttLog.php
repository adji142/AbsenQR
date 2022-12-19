<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_AttLog extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
		$this->load->model('LoginMod');
	}

	public function Read()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$TglAwal = $this->input->post('TglAwal');
		$TglAkhir = $this->input->post('TglAkhir');

		$SQL = "
			SELECT 
				a.NikKaryawan,
				b.NamaKaryawan,
				c.NamaJabtan	Jabatan,
				d.NamaDepartement Departement,
				DATE(a.TanggalLog) TglAbsen,
				MAX(CASE WHEN a.LogType = 1 THEN TIME(TanggalLog) END) CheckIn,
				MAX(CASE WHEN a.LogType = 0 THEN TIME(TanggalLog) END) CheckOut,
				a.ImageBase64
			FROM attandancelog a
			LEFT JOIN masterkaryawan b on a.NikKaryawan = b.Nik
			LEFT JOIN tjabatan c on b.KodeJabatan = c.KodeJabatan
			LEFT JOIN tdepartement d on b.KodeDepartement = d.KodeDepartment
			LEFT JOIN tdevice e on a.DeviceID = e.UniqKey
			WHERE DATE(a.TanggalLog) BETWEEN '$TglAwal' AND '$TglAkhir'
			GROUP BY a.NikKaryawan, DATE(a.TanggalLog)
		";

		$rs = $this->db->query($SQL);

		if ($rs->num_rows()>0) {
			$data['success'] = true;
			$data['data'] = $rs->result();
		}
		else{
			$data['message'] = 'No Record Found';
		}
		echo json_encode($data);
	}

	public function getImageAttLog()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$Nik = $this->input->post('Nik');
		$TglLog = $this->input->post('TglLog');

		// $rs = $this->ModelsExecuteMaster->FindData(array('NikKaryawan'=>$Nik,'TanggalLog'=>$TglLog),'attandancelog');

		$rs = $this->db->query("SELECT * FROM attandancelog WHERE NikKaryawan = '$Nik' AND DATE(TanggalLog) = '$TglLog' ");
		if ($rs->num_rows()>0) {
			$data['success'] = true;
			$data['data'] = $rs->result();
		}
		else{
			$data['message'] = 'No Record Found';
		}
		echo json_encode($data);
	}

	public function AddLog()
	{
		$data = array('success' => false ,'message'=>array(),'Nama'=>'','Posisi'=>'');

		$id				= $this->input->post('id');
		$NikKaryawan	= $this->input->post('NikKaryawan');
		$TanggalLog		= date("Y-m-d H:i:s");
		$LogType		= $this->input->post('LogType');
		$DeviceID		= $this->input->post('DeviceID');
		$ImageBase64 	= $this->input->post('picture_base64');

		$param = array(
			'id' => 0,
			'NikKaryawan' => $NikKaryawan,
			'TanggalLog' => $TanggalLog,
			'LogType' => $LogType,
			'DeviceID' => $DeviceID,
			'ImageBase64' => $ImageBase64
		);

		try {

			$SQL = "
				SELECT a.*, b.NamaJabtan, c.NamaDepartement,
				CASE WHEN a.Active = 1 THEN 'Aktif' ELSE 'InAktif' END StatusKaryawan
				FROM masterkaryawan a
				LEFT JOIN tjabatan b on a.KodeJabatan = b.KodeJabatan
				LEFT JOIN tdepartement c on KodeDepartement = c.KodeDepartment
				where a.Nik = '$NikKaryawan' And a.Active = 1
			";

			$dataKaryawan = $this->db->query($SQL);

			if ($dataKaryawan->num_rows() > 0) {
				foreach ($dataKaryawan->result() as $key) {
					$data['Nama'] = $key->NamaKaryawan;
					$data['Posisi'] = $key->NamaJabtan . ' , ' .$key->NamaDepartement;
				}
			}
			else{
				$data['success'] = false;
				$data['message'] = 'Karyawan Tidak ditemukan';
				goto jumpx;
			}

			$checkExist = $this->db->query("select * from attandancelog where NikKaryawan= '$NikKaryawan' and LogType = '$LogType' and date(TanggalLog) = '".date("Y-m-d")."' ");

			if ($checkExist->num_rows()>0) {
				$data['success'] = false;
				$data['message'] = 'Sudah Absen';
			}
			else{
				$call_x = $this->ModelsExecuteMaster->ExecInsert($param,'attandancelog');
				if ($call_x) {
					$data['success'] = true;
				}
				else{
					$undone = $this->db->error();
					$data['message'] = "Sistem Gagal Melakukan Pemrosesan Data : ".$undone['message'];
				}
			}
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "Gagal memproses data ". $e->getMessage();
		}


		jumpx:
		echo json_encode($data);
		
	}

}