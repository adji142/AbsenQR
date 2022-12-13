<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

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
		$this->load->model('LoginMod');
	}
	public function index()
	{
		$this->load->view('index');
	}
	function Log_Pro()
	{
		$data = array('success' => false ,'message'=>array(),'username'=>array(),'unique_id'=>array());
        $usr = $this->input->post('username');
		$pwd =$this->input->post('password');
		// var_dump($usr.' '.$pwd);
		$Validate_username = $this->LoginMod->Validate_username($usr);
		if($Validate_username->num_rows()>0){
			$userid = $Validate_username->row()->id;
			$pwd_decript =$Validate_username->row()->password;
			// var_dump($this->encryption->decrypt($pwd_decript));
			$pass_valid = $this->encryption->decrypt($Validate_username->row()->password);
			// var_dump($this->encryption->decrypt($Validate_username->row()->password));
			// $get_Validation = $this->LoginMod->Validate_Login($userid,$this->encryption->encrypt($pwd));
			if($pass_valid == $pwd){
				$sess_data['userid']=$userid;
				$sess_data['NamaUser'] = $Validate_username->row()->nama;
				$sess_data['username'] = $Validate_username->row()->username;
				$this->session->set_userdata($sess_data);
				$data['success'] = true;
				$data['username'] = $Validate_username->row()->username;
				$data['unique_id'] = $Validate_username->row()->id;
			}
			else{
				$data['success'] = false;
				$data['message'] = 'L-01'; // User password doesn't match
			}
		}
		else{
			$data['message'] = 'L-02'; // Username not found
		}
		// var_dump($data);
		echo json_encode($data);
	}
	function logout()
	{
		delete_cookie('ci_session');
        $this->session->sess_destroy();
        redirect('Welcome');
	}
	public function RegisterUser()
	{
		$data = array('success' => false ,'message'=>array(),'id' =>'');

		// parameter kode:kode,nama:nama,alamat:alamat,tlp:tlp,mail:mail,pj:pj,tgl:tgl,ket:ket}

		$uname = $this->input->post('uname');
		$nama = $this->input->post('nama');
		$mail = $this->input->post('mail');
		$pass = $this->input->post('pass');
		$role = $this->input->post('roles');

		$id = $this->input->post('id');
		$formtype = $this->input->post('formtype');
		$md_pass = $this->encryption->encrypt($pass);

		// 
		$insert = array(
			'username' 	=> $uname,
			'nama'		=> $nama,
			'email'		=> "",
			'password'	=> $md_pass,
		);
		if ($formtype == 'add') {
			$call = $this->ModelsExecuteMaster->ExecInsert($insert,'users');

			if ($call) {
				$xuser = $this->ModelsExecuteMaster->FindData(array('username'=>$uname),'users');
				if ($xuser->num_rows() > 0) {
					$insert = array(
						'userid' 	=> $xuser->row()->id,
						'roleid'	=> $role,
					);
					$call_x = $this->ModelsExecuteMaster->ExecInsert($insert,'userrole');
					if ($call_x) {
						$data['success'] = true;
					}
				}
			}
			else{
				$data['message'] = 'Data Gagal di input';
			}
		}
		elseif ($formtype == 'edit') {
			$rs = $this->ModelsExecuteMaster->ExecUpdate($insert,array('id'=>$id),'users');
			if ($rs) {
				$updaterole = $this->ModelsExecuteMaster->ExecUpdate(array('roleid'=>$role),array('userid'=>$id),'userrole');
				if ($updaterole) {
					$data['success'] = true;
				}
			}
			else{
				$data['success'] = false;
				$data['message'] = 'Gagal Updata Data';
			}
		}
		elseif ($formtype == 'delete') {
			try {
				$SQL = "DELETE FROM users WHERE id = ".$id;
				$rs = $this->db->query($SQL);
				if ($rs) {
					$data['success'] = true;
				}
			} catch (Exception $e) {
				$data['success'] = false;
				$data['message'] = "Gagal memproses data ". $e->getMessage();
			}
		}
		echo json_encode($data);
	}
	public function changepass()
	{
		$data = array('success' => false ,'message'=>array(),'id' =>'');

		$uname = $this->input->post('user');
		$newpass = $this->encryption->encrypt($this->input->post('pass'));
		$id = $this->input->post('id');
		$call =$this->ModelsExecuteMaster->ExecUpdate(array('password'=>$newpass),array('id'=>$id),'users');
		if ($call) {
			$data['success'] = true;
		}
		else{
			$data['message'] = 'Gagal Update password';
		}
		echo json_encode($data);
	}
	public function GetSidebar()
	{
		$data = array('success' => false ,'message'=>array(),'data' =>array());

		$id = $this->input->post('id');
		$call =$this->GlobalVar->GetSideBar($id,1,1);
		if ($call) {
			$data['success'] = true;
			$data['data'] = $call->result();
		}
		else{
			$data['message'] = 'Gagal Update password';
		}
		echo json_encode($data);
	}
	public function Getindex()
	{
		$data = array('success' => false ,'message'=>array(),'Nomor' => '');

		$Kolom = $this->input->post('Kolom');
		$Table = $this->input->post('Table');
		$Prefix = $this->input->post('Prefix');

		$SQL = "SELECT RIGHT(MAX(".$Kolom."),4)  AS Total FROM " . $Table . " WHERE LEFT(" . $Kolom . ", LENGTH('".$Prefix."')) = '".$Prefix."'";

		// var_dump($SQL);
		$rs = $this->db->query($SQL);

		$temp = $rs->row()->Total + 1;

		$nomor = $Prefix.str_pad($temp, 6,"0",STR_PAD_LEFT);
		if ($nomor != '') {
			$data['success'] = true;
			$data['nomor'] = $nomor;
		}
		echo json_encode($data);
	}
	public function ReadUser()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array(),'decript'=>'');

		$id = $this->input->post('id');

		$SQL = "SELECT a.*,c.rolename,b.roleid FROM users a
				LEFT JOIN userrole b on a.id = b.userid
				LEFT JOIN roles c on b.roleid = c.id ";
		if ($id != '') {
			$SQL .= ' WHERE a.id = '.$id;
		}
		// var_dump($SQL);
		$rs = $this->db->query($SQL);
		if ($id != '') {
			$rsx = $this->ModelsExecuteMaster->FindData(array('id'=>$id),'users');
			$data['decript'] = $this->encryption->decrypt($rsx->row()->password);
		}
		if ($rs->num_rows() > 0) {
			$data['success'] = true;
			$data['data'] = $rs->result();
		}
		echo json_encode($data);
	}
	public function read()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array(),'decript'=>'');

		$kriteria = $this->input->post('kriteria');
		$skrip = $this->input->post('skrip');
		$userid = $this->input->post('userid');
		$roleid = $this->input->post('roleid');
		
		$SQL = "";

		try {
			$SQL .= "
				SELECT 
					a.id UserId,
					a.username,
					a.nama,
					a.HakAkses,
					c.id RoleId,
					c.rolename,
					a.email,
					a.phone
				FROM users a
				LEFT JOIN userrole b on a.id = b.userid
				LEFT JOIN roles c on b.roleid = c.id 
				WHERE CONCAT(a.username,' ',a.nama) LIKE '%".$kriteria."%'
			";
			if ($userid != '') {
				$SQL .= " AND a.id =".$userid." ";
			}
			if ($roleid != '') {
				$SQL .= " AND c.id =".$roleid." ";
			}
			if ($skrip != '') {
				$SQL .= " AND ".$skrip." ";
			}

			// $SQL .= ' LIMIT 5';
			$rs = $this->db->query($SQL);

			if ($rs) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
			else{
				$undone = $this->db->error();
				$data['success'] = false;
				$data['message'] = $undone['message'];
			}
		} catch (Exception $e) {
			$undone = $this->db->error();
			$data['success'] = false;
			$data['message'] = $undone['message'];
		}

		echo json_encode($data);
	}
	public function GetAccess()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array(),'decript'=>'');

		$userid = $this->input->post('userid');

		$rs = $this->LoginMod->GetUser($userid);

		if ($rs) {
			$data['success'] = true;
			$data['data'] = $rs->result();
		}
		else{
			$undone = $this->db->error();
			$data['success'] = false;
			$data['
			message'] = $undone['message'];
		}
		echo json_encode($data);
	}
}
