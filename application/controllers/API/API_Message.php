<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Message extends CI_Controller {

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
	}
	public function SendInbox()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0,'data'=>array());

		$userid = $this->input->post('userid');
		$DeviceID = $this->input->post('DeviceID');
		$MessageDate= $this->input->post('MessageDate');
		$Message = $this->input->post('Message');
		$Read = $this->input->post('Read');

		$param = array(
			'userid' 		=> $userid,
			'DeviceID'		=> $DeviceID,
			'MessageDate'	=> date("Y-m-d h:i:sa"),
			'Message'		=> $Message,
			'Read'			=> 0
		);
		$rs = $this->ModelsExecuteMaster->ExecInsert($param,'inbox');
		if ($rs) {
			$data['success'] = true;
		}
		else{
			$data['success'] = false;
			$data['message'] = 'Gagal mengirim pesan';
		}
		echo json_encode($data);
	}
	public function SendOutbox()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0,'data'=>array());

		$userid = $this->input->post('userid');
		$DeviceID = $this->input->post('DeviceID');
		$Message = $this->input->post('Message');
		$Replyby = $this->input->post('Replyby');

		$param = array(
			'userid' 		=> $userid,
			'DeviceID'		=> $DeviceID,
			'MessageDate'	=> date("Y-m-d h:i:sa"),
			'Message'		=> $Message,
			'Read'			=> 0,
			'ReplyBy'		=> $Replyby
		);
		$rs = $this->ModelsExecuteMaster->ExecInsert($param,'outbox');
		if ($rs) {
			$data['success'] = true;
		}
		else{
			$data['success'] = false;
			$data['message'] = 'Gagal mengirim pesan';
		}
		echo json_encode($data);
	}
	public function ReadMessage()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0,'data'=>array());
		$userid = $this->input->post('kodeuser');

		$query = "SELECT * FROM ( 
					SELECT *,'inbox' tipe FROM outbox
					union
					SELECT *,'' reply,'outbox' tipe FROM inbox
				)a where a.userid = '".$userid."' ORDER BY MessageDate";
		$rs = $this->db->query($query);

		if ($rs->num_rows() > 0) {
			$data['success'] = true;
			$data['count'] = $rs->num_rows();
			$data['data'] = $rs->result();
		}

		echo json_encode($data);
	}
	public function ReadCountMessage()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0);
		$username = $this->input->post('username');
		$table = $this->input->post('table');

		$query = "select * from ".$table." where userid = '".$username."' and `Read` = 0";
		$rs = $this->db->query($query);

		$data['success'] = true;
		$data['count'] = $rs->num_rows();

		echo json_encode($data);
	}
	public function UpdateFlagread()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0);
		$username = $this->input->post('username');
		$table = $this->input->post('table');

		$rs = $this->ModelsExecuteMaster->ExecUpdate(array('`Read`'=>1),array('userid'=> $username,'`Read`'=>0),$table);
		// $query = "update ".$table." set `Read` = 1 where userid ='".$userid."' and `Read` = 0";
		// $rs = $this->db->query($query);
		if ($rs) {
			$data['success'] = true;
		}
		else{
			$data['success'] = false;
			$data['message'] = 'Gagal Menyimpan Data';	
		}
		echo json_encode($data);
	}
}