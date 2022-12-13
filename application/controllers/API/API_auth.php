<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
class API_auth extends CI_Controller {

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

		// require APPPATH.'libraries/phpmailer/src/Exception.php';
  //       require APPPATH.'libraries/phpmailer/src/PHPMailer.php';
  //       require APPPATH.'libraries/phpmailer/src/SMTP.php';
	}
	public function FindUserName()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0,'username'=>array(),'email'=>array(),'phone'=>array());

		$username = $this->input->post('username');

		$query = "Select * from users where username = '".$username."'";

		$rs = $this->db->query($query);

		if ($rs->num_rows() > 0) {
			$data['success'] = true;
			$data['message'] = "Username ".$username." sudah ada.";
			$data['username'] = $rs->row()->username;
			$data['email'] = $rs->row()->email;
			$data['phone'] = $rs->row()->phone;
		}

		echo json_encode($data);
	}

	public function FindEmail()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0,'data'=>array());

		$email = $this->input->post('email');
		$MobileToken = $this->input->post('MobileToken');

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$query = "Select * from users where email = '".$email."'";

			$rs = $this->db->query($query);
			// var_dump(strpos($email, "@"));
			if (strpos($email, "@") == false) {
				$data['success'] = true;
				$data['message'] = "Email Tidak valid.";
			}
			else if ($rs->num_rows() > 0) {
				$data['success'] = true;
				$data['message'] = "Username ".$email." sudah ada.";
			}
		}
		echo json_encode($data);
	}
	public function FindPhone()
	{
		$data = array('success' => false ,'message'=>array(),'count'=>0,'data'=>array());

		$phone = $this->input->post('phone');
		$MobileToken = $this->input->post('MobileToken');

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$query = "Select * from users where phone = '".$phone."'";

			$rs = $this->db->query($query);
			// var_dump(strpos($email, "@"));
			if ($rs->num_rows() > 0) {
				$data['success'] = true;
				$data['message'] = "No. Telepon : ".$phone." sudah ada.";
				$data['data'] = $rs->result();
			}
		}
		echo json_encode($data);
	}
	public function RegisterUser()
	{
		$data = array('success' => false ,'message'=>array(),'id' =>'');

		// parameter kode:kode,nama:nama,alamat:alamat,tlp:tlp,mail:mail,pj:pj,tgl:tgl,ket:ket}

		$username = $this->input->post('username');
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$pass = $this->input->post('pass');
		// $role = $this->input->post('role');
		$md_pass = $this->encryption->encrypt($pass);

		// 
		$insert = array(
			'username' 	=> $username,
			'nama'		=> $username,
			'email'		=> $email,
			'password'	=> $md_pass,
			'phone'		=> $phone,
			'createdon'	=> date("Y-m-d h:i:sa"),
		);

		$call = $this->ModelsExecuteMaster->ExecInsert($insert,'users');

		if ($call) {
			$xuser = $this->ModelsExecuteMaster->FindData(array('username'=>$username),'users');
			if ($xuser->num_rows() > 0) {
				$insert = array(
					'userid' 	=> $xuser->row()->id,
					'roleid'	=> 4,
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
		echo json_encode($data);
	}
	function Log_Pro()
	{
		$data = array('success' => false ,'message'=>array(),'username'=>array(),'unique_id'=>array(),'email' => array(),'role'=>'');

        $usr = $this->input->post('username');
		$pwd =$this->input->post('pass');
		$androidid = $this->input->post('androidid');
		$device = $this->input->post('device');
		$isReset = $this->input->post('isReset');
		// var_dump($usr.' '.$pwd);
		$SQL = "
			SELECT * FROM users where phone = '".$usr."';
		";
		$cekExist = $this->db->query($SQL);

		// if ($cekExist->row()->HardwareID =='') {

			$Validate_phone = $this->LoginMod->Validate_phone($usr);
			// var_dump($Validate_username);
			if($Validate_phone->num_rows()>0){
				$SQL = "
					SELECT * FROM users where phone = '$usr'
				";
				$x = $this->db->query($SQL);

				if ($x->num_rows() > 0) {
					$userid = $Validate_phone->row()->id;
					$pwd_decript =$Validate_phone->row()->password;
					$pass_valid = $this->encryption->decrypt($Validate_phone->row()->password);
					if($pass_valid == $pwd){

						$paramUpdate = array(
							'browser'	=> $device,
							'HardwareID'=> $androidid
						);

						$updateState = $this->ModelsExecuteMaster->ExecUpdate($paramUpdate,array('phone'=> $usr),'users');

						if ($updateState) {
							$data['success'] = true;
							$data['username'] = $Validate_phone->row()->username;
							$data['unique_id'] = $Validate_phone->row()->RecordOwnerID;
							$data['email'] = $Validate_phone->row()->email;
						}
						else{
							$undone = $this->db->error();
							$data['message'] = "Sistem Gagal Melakukan Pemrosesan Data : ".$undone['message'];
						}
					}
					else{
						$data['success'] = false;
						$data['message'] = 'Password tidak valid'; // User password doesn't match
					}
				}
				else{
					$data['success'] = false;
					$data['message'] = 'User sudah login di device '.$cekExist->row()->browser;
				}
			}
			else{
				$data['success'] = false;
				$data['message'] = 'Username Tidak ditemukan'; // Username not found
			}
		// }
		// else{
		// 	$data['success'] = false;
		// 	$data['message'] = 'User sudah login di device '.$cekExist->row()->browser;
		// }
		// var_dump($data);
		echo json_encode($data);
	}
	public function ChangePassword()
	{
		$data = array('success' => false ,'message'=>array());
		$usr = $this->input->post('username');
		$pwd =$this->input->post('pass');

		$Encrptpass = $this->encryption->encrypt($pwd);

		$row = array(
			'password'	=> $Encrptpass
		);

		$rs = $this->ModelsExecuteMaster->ExecUpdate($row,array('username'=> $usr),'users');
		if ($rs) {
			$data['success'] = true;
		}
		else{
			$data['message'] = 'Gagal Update Password';
		}
		echo json_encode($data);
	}
	public function ChangeImage()
	{
		$data = array('success' => false ,'message'=>array(),'imageurl'=>array());
		$usr = $this->input->post('username');
		$image = $this->input->post('baseimage');
		$imagename = $this->input->post('imagename');

		// var_dump($image.' ; '.$imagename);
		$temp = base64_decode($image);
		$link = 'storeimage/'.$imagename;
		try {
			file_put_contents($link, $temp);	
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		$fulllink = base_url().$link;

		$rs = $this->ModelsExecuteMaster->ExecUpdate(array('ImageProfile'=>$fulllink),array('username'=> $usr),'users');
		if ($rs) {
			$data['success'] = true;
			$data['imageurl'] = $fulllink;
		}
		else{
			$data['message'] = 'Gagal Update Password';
		}
		echo json_encode($data);
	}
	public function GetUserInfo()
	{
		$data = array('success' => false ,'message'=>array(),'username'=>'','email'=>'','phone'=>'','ImageProfile'=>'');
		$usr = $this->input->post('username');

		$sql = "SELECT username,email,phone,ImageProfile FROM users where username = '".$usr."'";
		$rs = $this->db->query($sql);

		if ($rs->num_rows() > 0) {
			$data['success'] = true;
			$data['username'] = $rs->row()->username;
			$data['email'] = $rs->row()->email;
			$data['phone'] = $rs->row()->phone;
			$data['ImageProfile'] = $rs->row()->ImageProfile;
		}
		else{
			$data['success'] = false;
			$data['message'] = 'Fail to generate data';
		}
		echo json_encode($data);
	}
	function send_email(){
		$data = array('success' => false ,'message'=>array());
        $param = $this->input->post('email');
        $Cek_Already = $this->ModelsExecuteMaster->FindData(array('email'=>$param),'users');
        $this->load->library('email');

        // run random

        $chars = array(
	        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
	        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
	        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
	        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
	        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
	    );

	    shuffle($chars);

	    $num_chars = count($chars) - 1;
	    $token = '';

	    for ($i = 0; $i < $num_chars; $i++){ // <-- $num_chars instead of $len
	        $token .= $chars[mt_rand(0, $num_chars)];
	    }

        if ($Cek_Already->num_rows() > 0) {
        	$this->load->library('encryption');
        	$username = $Cek_Already->row()->username;
        	$password = $token;

        	if ($password <> '') {
        		$cript_pass = '';
        		$rs = $this->ModelsExecuteMaster->ExecUpdate(array('password'=>$this->encryption->encrypt(substr($password, 0,6))),array('email'=> $param),'users');
				if ($rs) {
					$data['success'] = true;
				}
				else{
					$data['message'] = 'Gagal Update Password';
				}
        	}

        	// $xdata = $this->ModelsExecuteMaster->FindData(array('id'=>1),'temailsetting');
        	// var_dump($xdata->row()->smtp_host);	
   //      	$response = false;
			// $mail = new PHPMailer();

			// // SMTP configuration
	  //       $mail->isSMTP();
	  //       $mail->Host     = 'smtp.gmail.com'; //sesuaikan sesuai nama domain hosting/server yang digunakan
	  //       $mail->SMTPAuth = true;
	  //       $mail->Username = 'aissystemsolo@gmail.com'; // user email
	  //       $mail->Password = 'eijugplezooyxzeo'; // password email
	  //       $mail->SMTPSecure = 'ssl';
	  //       $mail->Port     = 465;

	  //       $mail->setFrom('aissystemsolo@gmail.com', ''); // user email
	  //       $mail->addReplyTo('aissystemsolo@gmail.com', ''); //user email//user email

	        $to = $param;
	        $subject = '[No-Replay]Rahasia !!! Reset password SpiritBooks Apps[No-Replay]';
	        $message = '
	        	<h3><center><b>Spiritbooks</b></center></h3><br>
	            <p>
	            Berikut detaik akun anda di <a href="https://renungan-spirit.com/">renungan-spirit.com</a><br>
	            <b>Jangan berikan email ini ke siapapun termasuk staff dari pengelola aplikasi</b>
	            <br>
	            </p>
	            <pre>
	            	Email 		: '.$param.' <br>
	            	Username 	: '.$username.'<br>
	            	Password 	: '.substr($password, 0,6).'
	            <p>
	            <br>
	            Best Regards<br><br>
	            <a href="renungan-spirit.com">renungan-spirit.com</a>
	            </p>
	        ';

	        $this->ModelsExecuteMaster->SendSpesificEmail($to,$subject,$message);
	        // Add a recipient
	        // $mail->addAddress($param); //email tujuan pengiriman email

	        // // Email subject
	        // $mail->Subject = $subject; //subject email

	        // // Set email format to HTML
	        // $mail->isHTML(true);

	        // $mail->Body = $message;
	        // if($mail->send()){
	        //     $data['success']=true;
	        // }
	        // else{
	        //     $data['message']=$mail->ErrorInfo;
	        // }
        }
        else{
        	$data['message'] = 'Email tidak ditemukan';
        }
        echo json_encode($data);
    }
    public function run_key() {

	    $chars = array(
	        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
	        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
	        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
	        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
	        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '?', '!', '@', '#',
	        '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '|', ';', '/', '=', '+'
	    );

	    shuffle($chars);

	    $num_chars = count($chars) - 1;
	    $token = '';

	    for ($i = 0; $i < $num_chars; $i++){ // <-- $num_chars instead of $len
	        $token .= $chars[mt_rand(0, $num_chars)];
	    }

	    return $token;
	}
	public function SaldoAccount(){
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$KodeUser = $this->input->post('username');
		$token = $this->input->post('token');

		if ($token != "") {
			$SQL = "";

			$SQL = "SELECT 
						u.username, u.email, COALESCE(d.TopUp,0) TopUp, COALESCE(d.PembelianBuku,0) PembelianBuku,
						COALESCE(d.TopUp,0) - COALESCE(d.PembelianBuku,0) Saldo
					FROM users u
					LEFT JOIN (
						SELECT 
							b.userid,
							SUM(COALESCE(a.GrossAmt,0) - COALESCE(b.Adminfee,0)) TopUp,
							COALESCE(c.Pembelian,0) PembelianBuku
						FROM topuppayment a
						INNER JOIN thistoryrequest b on a.NoTransaksi = b.NoTransaksi AND a.Mid_TransactionStatus = 'settlement'
						LEFT JOIN(
							SELECT x.UserID, SUM(COALESCE(x.Qty,0) * COALESCE(x.Harga,0)) Pembelian FROM transaksi x
						where x.StatusTransaksi IN(1,99)
						) c on c.UserID = b.userid
						GROUP BY b.userid
					) d on u.username = d.userid 
					WHERE u.username = '".$KodeUser."'
					";

			$SQL = "CALL getSaldoUser('".$KodeUser."')";

			$SQL = "SELECT username, Saldo FROM users where username = '".$KodeUser."'";
			try {
				$rs = $this->db->query($SQL);
				if ($rs) {
					$data['success'] = true;
					$data['data'] = $rs->result();
				}
				else{
					$data['success'] = false;
					$undone = $this->db->error();
					$data['message'] = 'Gagal Melakukan Pemrosesan data : ' . $undone['message'];
				}
			} catch (Exception $e) {
				$data['success'] = false;
				$data['message'] = $e->getMessage();
			}
		}
		else{
			$data['success'] = false;
			$data['message'] = "Invalid Token";
		}
		echo json_encode($data);
	}
	public function logout()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());
		$KodeUser = $this->input->post('username');
		$token = $this->input->post('token');
		try {
			$rs = $this->ModelsExecuteMaster->ExecUpdate(array('browser'=>'','HardwareID'=>''), array('username'=>$KodeUser),'users');
			if ($rs) {
				$data['success'] = true;
			}
			else{
				$data['success'] = false;
				$undone = $this->db->error();
				$data['message'] = 'Gagal Melakukan Pemrosesan data : ' . $undone['message'];
			}
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = $e->getMessage();
		}
		echo json_encode($data);
	}

	public function UpdateToken()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array());
		$KodeUser = $this->input->post('username');
		$token = $this->input->post('token');

		$param = array('token'=>$token);

		$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('username'=>$KodeUser),'users');
		if ($rs) {
			$data['success'] = true;
		}
		echo json_encode($data);
	}
}