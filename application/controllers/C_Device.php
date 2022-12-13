<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_Device extends CI_Controller {
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

		$KodeDevice = $this->input->post('KodeDevice');

		if ($KodeDevice == '') {
			$rs = $this->ModelsExecuteMaster->GetData('tdevice');
		}
		else{
			$rs = $this->ModelsExecuteMaster->FindData(array('KodeDevice'=>$KodeDevice),'tdevice');
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

	public function CRUD()
	{
		$data = array('success' => false ,'message'=>array());

		$KodeDevice 	= $this->input->post('KodeDevice');
		$NamaDevice 	= $this->input->post('NamaDevice');
		$JenisDevice	= $this->input->post('JenisDevice');
		$UniqKey		= $this->input->post('UniqKey');

		// $exploder = explode("|",$ItemGroup[0]);
		$formtype = $this->input->post('formtype');

		$param = array(
			'KodeDevice' 	=> $KodeDevice,
			'NamaDevice'	=> $NamaDevice,
			'JenisDevice' 	=> $JenisDevice,
			'UniqKey' 		=> $UniqKey
		);
		if ($formtype == 'add') {
			$this->db->trans_begin();
			try {
				$call_x = $this->ModelsExecuteMaster->ExecInsert($param,'tdevice');
				if ($call_x) {
					$this->db->trans_commit();
					$data['success'] = true;
				}
				else{
					$undone = $this->db->error();
					$data['message'] = "Sistem Gagal Melakukan Pemrosesan Data : ".$undone['message'];
					goto jump;
				}
			} catch (Exception $e) {
				jump:
				$this->db->trans_rollback();
				// $data['success'] = false;
				// $data['message'] = "Gagal memproses data ". $e->getMessage();
			}
		}
		elseif ($formtype == 'edit') {
			try {
				$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('KodeDevice'=> $KodeDevice),'tdevice');
				if ($rs) {
					$data['success'] = true;
				}
				else{
					$undone = $this->db->error();
					$data['message'] = "Sistem Gagal Melakukan Pemrosesan Data : ".$undone['message'];
				}
			} catch (Exception $e) {
				$data['success'] = false;
				$data['message'] = "Gagal memproses data ". $e->getMessage();
			}
		}
		elseif ($formtype == 'delete') {
			try {
				$SQL = "DELETE FROM tdevice WHERE KodeDevice = '".$KodeDevice."'";
				$rs = $this->db->query($SQL);
				if ($rs) {
					$data['success'] = true;
				}
				else{
					$undone = $this->db->error();
					$data['message'] = "Sistem Gagal Melakukan Pemrosesan Data : ".$undone['message'];
				}
			} catch (Exception $e) {
				$data['success'] = false;
				$data['message'] = "Gagal memproses data ". $e->getMessage();
			}
		}
		else{
			$data['success'] = false;
			$data['message'] = "Invalid Form Type";
		}
		jumpx:
		echo json_encode($data);
	}

	public function GetUniqID()
	{
		$data = array('success' => false ,'message'=>array(),'DeviceID'=>'');

		if(isset($_COOKIE["deviceIdentifier"])) {
		    // there is already a cookie set; we know the device
		    $data['success'] = true;
		    $data['DeviceID'] = $_COOKIE["deviceIdentifier"];
		    // echo "This is the Device: ".$_COOKIE["deviceIdentifier"];
		} else {
		    // there is no cookie set; a new device has connected
		    $dIdentifier = md5(time());
		    // set a new cookie for the device
		    setcookie("deviceIdentifier",$dIdentifier,time() * 2);
		    // echo "A new Device has ben recognized, it is now linked with the ID: ".$dIdentifier;
		    $data['success'] = true;
		    $data['DeviceID'] = $dIdentifier;
		}

		echo json_encode($data);
	}
}