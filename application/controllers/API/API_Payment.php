<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Payment extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		require_once(APPPATH.'libraries/midtrans/Midtrans.php');
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}
	public function getMetodePembayaran(){
		$data = array('success' => false ,'message'=>array(),'data' => array(), 'token'=>'');

		$token = $this->input->post('token');
		$kode = $this->input->post('kode');
		if ($token != "") {
			$SQL = "";
			$SQL .= "SELECT * FROM tpaymentmethod WHERE Active = 1 ";
			// var_dump($SQL);
			if ($kode != '') {
				$SQL .= " AND id ='$kode' ";
			}
			$SQL .= " ORDER BY indexshow ";
			$rs = $this->db->query($SQL);

			if ($rs) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
		}
		else{
			$data['message'] = "Invalid Token";
		}
		echo json_encode($data);
	}

	public function getLookup(){
		$data = array('success' => false ,'message'=>array(),'data' => array(), 'token'=>'');

		$token = $this->input->post('token');
		$kode = $this->input->post('kode');
		if ($token != "") {
			$SQL = "";
			$SQL .= "SELECT id ID, NamaMedia Title, CONCAT(NomorAkunPembayaran,' A/N ',NamaPemilikAkun) Subtitle FROM tpaymentmethod WHERE Active = 1 ";
			// var_dump($SQL);
			if ($kode != '') {
				$SQL .= " AND id ='$kode' ";
			}
			$SQL .= " ORDER BY indexshow ";
			$rs = $this->db->query($SQL);

			if ($rs) {
				$data['success'] = true;
				$data['data'] = $rs->result();
			}
		}
		else{
			$data['message'] = "Invalid Token";
		}
		echo json_encode($data["data"]);
	}

	public function MakePayment()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array(), 'token'=>'','NoTransaksi'=>'');

		$amt = $this->input->post('amt');
		$Adminfee = $this->input->post('Adminfee');
		$first_name = $this->input->post('first_name');
		$email = $this->input->post('email');
		$token = $this->input->post('token');
		$Periode = strval(date("Y")).strval(date("m"));
		// Set your Merchant Server Key
		\Midtrans\Config::$serverKey = $this->ModelsExecuteMaster->midTransServerKey();
		// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
		\Midtrans\Config::$isProduction = $this->ModelsExecuteMaster->midTransProduction();
		// Set sanitization on (default)
		\Midtrans\Config::$isSanitized = true;
		// Set 3DS transaction for credit card to true
		\Midtrans\Config::$is3ds = true;
		
		$md5dong = md5($email.rand());
		$order_id = $Periode.strval(rand()).substr($md5dong, 0,8);
		$params = array(
		    'transaction_details' => array(
		        'order_id' => $order_id,
		        'gross_amount' => $amt + $Adminfee,
		    ),
		    'customer_details' => array(
		        'first_name' => $first_name,
		        'email' => $email
		    ),
		);
		if ($token!= "") {
			try {
				$snapToken = \Midtrans\Snap::getSnapToken($params);
				
				$param = array(
					'NoTransaksi' => $order_id,
					'TglTransaksi' => date("Y/m/d hh:mm:ss"),
					'TokenMidtrans' => $snapToken,
					'userid' 		=> $first_name,
					'GrossAmt'		=> $amt,
					'Adminfee'		=> $Adminfee
				);

				$this->ModelsExecuteMaster->ExecInsert($param,'thistoryrequest');

				$getEmail = "
						SELECT b.username,b.email FROM thistoryrequest a
						LEFT JOIN users b on a.userid = b.username
						WHERE a.NoTransaksi = '".$order_id."'
					";
				$email = $this->db->query($getEmail)->row()->email;
				$this->ModelsExecuteMaster->PushEmail('payment',$order_id,$email);
				$this->ModelsExecuteMaster->PushEmail('notification',$order_id,'');

				$data['token'] = $snapToken;
				$data['NoTransaksi'] = $order_id;
				$data['success'] = true;
			} catch (Exception $e) {
				$data['message'] = $e->getMessage();
				$data['success'] = false;
			}
		}
		else{
			$data['message'] = "InvalidToken";
			$data['success'] = false;
		}

		// var_dump($snapToken);
		echo json_encode($data);
	}
	public function CheckTransaction()
	{
		$data = array('success' => false ,'message'=>array(),'data' => array(), 'token'=>'');

		$NoTransaksi = $this->input->post('NoTransaksi');
		// var_dump($NoTransaksi);
		\Midtrans\Config::$serverKey = $this->ModelsExecuteMaster->midTransServerKey();
		// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
		\Midtrans\Config::$isProduction = $this->ModelsExecuteMaster->midTransProduction();
		// Set sanitization on (default)
		\Midtrans\Config::$isSanitized = true;
		// Set 3DS transaction for credit card to true
		\Midtrans\Config::$is3ds = true;
		try {
			$status = \Midtrans\Transaction::status($NoTransaksi);
			$data['data'] = $status;
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = $e->getMessage();
		}
		echo json_encode($data);
	}
	public function GetNotif(){
		// var_dump($order_id);
		// $data = parse_str($_SERVER['QUERY_STRING'], $_GET); 
		$order_id = $this->input->get('order_id');
		$status_code = $this->input->get('status_code');
		$transaction_status = $this->input->get('transaction_status');
		$data['order_id'] 				= $order_id;
		$data['status_code'] 			= $status_code;
		$data['transaction_status'] 	= $transaction_status;
		// var_dump($data);
		// $getEmail = "
		// 				SELECT b.username,b.email FROM thistoryrequest a
		// 				LEFT JOIN users b on a.userid = b.username
		// 				WHERE a.NoTransaksi = '".$order_id."'
		// 			";
		// $email = $this->db->query($getEmail)->row()->email;
		// $this->ModelsExecuteMaster->PushEmail('payment',$order_id,$email);

		$this->load->view('V_API/paymentfinish',$data);
	}

	public function RecordPayment(){
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$NoTransaksi = $this->input->post('NoTransaksi');
		$TglTransaksi = $this->input->post('TglTransaksi');
		$TglPencatatan = $this->input->post('TglPencatatan');
		$MetodePembayaran = $this->input->post('MetodePembayaran');
		$GrossAmt = $this->input->post('GrossAmt');
		$AdminFee = $this->input->post('AdminFee');
		$Mid_PaymentType = $this->input->post('Mid_PaymentType');
		$Mid_TransactionID = $this->input->post('Mid_TransactionID');
		$Mid_MechantID = $this->input->post('Mid_MechantID');
		$Mid_Bank = $this->input->post('Mid_Bank');
		$Mid_VANumber = $this->input->post('Mid_VANumber');
		$Mid_SignatureKey = $this->input->post('Mid_SignatureKey');
		$Mid_TransactionStatus = $this->input->post('Mid_TransactionStatus');
		$Mid_FraudStatus = $this->input->post('Mid_FraudStatus');
		$UserID = $this->input->post('UserID');

		$param = array(
			'NoTransaksi' => $NoTransaksi,
			'TglTransaksi' => $TglTransaksi,
			'TglPencatatan' => $TglPencatatan,
			'MetodePembayaran' => $MetodePembayaran,
			'GrossAmt' => $GrossAmt,
			'AdminFee' => $AdminFee,
			'Mid_PaymentType' => $Mid_PaymentType,
			'Mid_TransactionID' => $Mid_TransactionID,
			'Mid_MechantID' => $Mid_MechantID,
			'Mid_Bank' => $Mid_Bank,
			'Mid_VANumber' => $Mid_VANumber,
			'Mid_SignatureKey' => $Mid_SignatureKey,
			'Mid_TransactionStatus' => $Mid_TransactionStatus,
			'Mid_FraudStatus' => $Mid_FraudStatus,
			'UserID'		=> $UserID
		);

		try {
			$exist = $this->ModelsExecuteMaster->FindData(array('NoTransaksi'=>$NoTransaksi),'topuppayment');
			if ($exist->num_rows() > 0) {
				$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('NoTransaksi'=>$NoTransaksi),'topuppayment');
			}
			else{
				$rs = $this->ModelsExecuteMaster->ExecInsert($param,'topuppayment');

				// Send Email
				$rs_user = $this->ModelsExecuteMaster->FindData(array('username'=>$UserID),'users')->row();

				if ($rs_user->email <> '') {
					$reciept = $rs_user->email;
					$subject = "Notifikasi Pembayaran";
					$body = $this->ModelsExecuteMaster->DefaultBody();
					$body .= $this->ModelsExecuteMaster->Email_payment($NoTransaksi);

					try{
						if ($rs_user->token != '') {
							$this->ModelsExecuteMaster->PushNotification($rs_user->token);
						}
					}
					catch (Exception $e) {
						$data['message'] = $e->getMessage();
					}
				}
			}
			if ($rs) {
				$data['success'] = true;
			}	
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
			$data['success'] = false;
		}
		echo json_encode($data);
	}
	public function getPaymentHistory(){
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$userid = $this->input->post('userid');
		$NoTransaksi = $this->input->post('NoTransaksi');

		$SQL  = "SELECT * FROM topuppayment a
				where a.read = 0 ";
		if ($userid != "") {
			$SQL .= " AND userid = '".$userid."' ";
		}

		$rs = $this->db->query($SQL);

		if ($rs) {
			$data['success'] = true;
			$data['data'] = $rs->result();
		}
		else{
			$data['success'] = false;
		}
		echo json_encode($data);
	}

	public function cekPaymentStatus(){
		$data = array('success' => false ,'message'=>array(),'data' => array(),'datagateway'=>array(), 'url'=>'');

		\Midtrans\Config::$serverKey = $this->ModelsExecuteMaster->midTransServerKey();
		\Midtrans\Config::$isProduction = $this->ModelsExecuteMaster->midTransProduction();
		\Midtrans\Config::$isSanitized = true;
		\Midtrans\Config::$is3ds = true;

		$userid = $this->input->post('userid');
		$NoTransaksi = $this->input->post('NoTransaksi');
		$checktype = $this->input->post('checktype');
		// 1 = Check and Append
		// 0 = Only Status Cek
		if ($checktype == 1) {
			$SQL = "SELECT a.NoTransaksi, a.userid, b.Mid_TransactionStatus FROM thistoryrequest a
				LEFT JOIN topuppayment b on a.NoTransaksi = b.NoTransaksi
				WHERE (COALESCE(b.Mid_TransactionStatus,'') != 'settlement' OR COALESCE(b.Mid_TransactionStatus,'') = '') AND a.userid = '".$userid."'";

			$SQL = " SELECT NoTransaksi,UserID,Mid_TransactionStatus FROM topuppayment WHERE (COALESCE(Mid_TransactionStatus,'') != 'settlement' OR COALESCE(Mid_TransactionStatus,'') = '') AND UserID = '".$userid."' ";	
			if ($NoTransaksi != "") {
				$SQL .= " AND NoTransaksi = '".$NoTransaksi."' ";
			}

			$rs = $this->db->query($SQL);

			if ($rs) {
				$datatable = $rs->result();
				// var_dump($datatable);
				foreach ($datatable as $key) {
					try {
						// Get Transaction Status in Midtrans
						$status = \Midtrans\Transaction::status($key->NoTransaksi);
						$data['data'] = $status;
						// var_dump($status);
						if ($status) {
							// var_dump($status);
							$FindData = $this->ModelsExecuteMaster->FindData(array('NoTransaksi'=>$key->NoTransaksi),'topuppayment');

							if ($FindData->num_rows() >0) {
								$param = array(
									'TglPencatatan' => date("Y-m-d h:i:sa"),
									'Mid_TransactionStatus' => $status->transaction_status
								);
								// var_dump($param);
								$updateStatus = $this->ModelsExecuteMaster->ExecUpdate($param,array('NoTransaksi'=>$key->NoTransaksi),'topuppayment');

								if ($updateStatus) {
									$data['success'] = true;
								}
							}
							else{
								if ($status->payment_type == "bank_transfer") {
									$param = array(
										'NoTransaksi' => $key->NoTransaksi,
										'TglTransaksi' => $status->transaction_time,
										'TglPencatatan' => date("Y-m-d h:i:sa"),
										'MetodePembayaran' => $status->payment_type,
										'GrossAmt' => $status->gross_amount,
										'AdminFee' => 0,
										'Mid_PaymentType' => $status->payment_type,
										'Mid_TransactionID' => $status->transaction_id,
										'Mid_MechantID' => $status->merchant_id,
										'Mid_Bank' => $status->va_numbers[0]->bank,
										'Mid_VANumber' => $status->va_numbers[0]->va_number,
										'Mid_SignatureKey' => $status->signature_key,
										'Mid_TransactionStatus' => $status->transaction_status,
										'Mid_FraudStatus' => $status->fraud_status,
									);
								}
								elseif ($status->payment_type == "gopay") {
									$param = array(
										'NoTransaksi' => $key->NoTransaksi,
										'TglTransaksi' => $status->transaction_time,
										'TglPencatatan' => date("Y-m-d h:i:sa"),
										'MetodePembayaran' => $status->payment_type,
										'GrossAmt' => $status->gross_amount,
										'AdminFee' => 0,
										'Mid_PaymentType' => $status->payment_type,
										'Mid_TransactionID' => $status->transaction_id,
										'Mid_MechantID' => $status->merchant_id,
										'Mid_Bank' => '',
										'Mid_VANumber' => '',
										'Mid_SignatureKey' => $status->signature_key,
										'Mid_TransactionStatus' => $status->transaction_status,
										'Mid_FraudStatus' => $status->fraud_status,
									);
								}
								$rs = $this->ModelsExecuteMaster->ExecInsert($param,'topuppayment');
								if ($rs) {
									$data['success'] = true;
								}
							}
							// Notifikasi

							$FindDataPushNotif = $this->ModelsExecuteMaster->FindData(array('BaseRef'=>$key->NoTransaksi),'tpushemail');

							if ($FindDataPushNotif->num_rows() == 0 && $status->transaction_status == "settlement" ) {

								$getEmail = "
									SELECT b.username,b.email FROM topuppayment a
									LEFT JOIN users b on a.userid = b.username
									WHERE a.NoTransaksi = '".$key->NoTransaksi."'
								";
								$email = $this->db->query($getEmail)->row()->email;
								$this->ModelsExecuteMaster->PushEmail('payment_done',$key->NoTransaksi,$email);
							}
						}
					} catch (Exception $e) {
						$data['success'] = true;
						$data['message'] = $e->getMessage();
					}
					
					// $param = array(
					// 	'TglPencatatan' => date("Y-m-d h:i:sa"),
					// 	''
					// );
				}
			}
		}
		else{
			$x = "SELECT * FROM topuppayment where NoTransaksi = '$NoTransaksi'";
			$rs = $this->db->query($x)->row();
			try{
				$status = \Midtrans\Transaction::status($NoTransaksi);
				if ($status) {
					$data['data'] = $status;
					$data['success'] = true;
					$data['url'] = $rs->Attachment;
				}
			}
			catch (Exception $e) {
				$data['success'] = true;
				$data['message'] = $e->getMessage();
			}
		}

		echo json_encode($data);
	}

	public function getTransactionHistory(){
		$data = array('success' => false ,'message'=>array(),'data' => array());

		$userid = $this->input->post('userid');
		$tglawal = $this->input->post('tglawal');
		$tglakhir = $this->input->post('tglakhir');

		$SQL = "SELECT * FROM (
				SELECT 
					'0' Transaksi, B.NoTransaksi,B.TglTransaksi, CASE WHEN B.MetodePembayaran = 'MANUAL' THEN COALESCE(B.GrossAmt,0) + COALESCE(B.Adminfee,0) ELSE COALESCE(B.GrossAmt,0) - COALESCE(B.Adminfee,0) END Nominal,
					B.MetodePembayaran, B.userid, B.Mid_TransactionStatus,CONCAT('Top Up ','- ',UPPER(B.MetodePembayaran)) Keterangan,
					COALESCE(B.Mid_Bank,'') Bank, COALESCE(B.Mid_VANumber,'') VA_Numb,COALESCE(B.Mid_PaymentType,'') Mid_PaymentType,COALESCE(B.Attachment,'') Attachment,
					COALESCE(B.Adminfee,0) Adminfee
				FROM topuppayment B 
				WHERE DATE(B.TglTransaksi) BETWEEN '$tglawal' AND '$tglakhir' AND B.userid = '$userid'

				UNION ALL
				-- 1: Berhasil, 2 : Pending, 3: Gagal, 4:Return
				SELECT 
					a.StatusTransaksi, a.NoTransaksi, a.TglTransaksi, COALESCE(a.Qty ,0) * COALESCE(a.harga,0),
					CONCAT('Pembelian Buku ', b.judul) 'Keterangan', a.userid,
					CASE WHEN a.StatusTransaksi = 1 THEN 'Berhasil' ELSE 
						CASE WHEN a.StatusTransaksi = 2 THEN 'Pending' ELSE 
							CASE WHEN a.StatusTransaksi = 3 THEN 'Gagagl' ELSE 
								CASE WHEN a.StatusTransaksi = 4 THEN 'Return' ELSE 'Undefind Transaction' END
							END 
						END 
					END TransStatus,
					'Pembelian Buku ',
					'' Bank, '' VA_Numb,'' Mid_PaymentType, '', 0 
				FROM transaksi a
				LEFT JOIN tbuku b on a.KodeItem = b.KodeItem
				WHERE DATE(TglTransaksi) BETWEEN '$tglawal' AND '$tglakhir' AND userid = '$userid'
			)trx
			ORDER BY TglTransaksi DESC 
		";

		try{
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
		}catch (Exception $e) {
			$data['success'] = true;
			$data['message'] = $e->getMessage();
		}

		echo json_encode($data);
	}
	public function chargeGopay(){
		$data = array('success' => false ,'message'=>array(),'data' => array());
		$amt = $this->input->post('amt');
		$Adminfee = $this->input->post('Adminfee');
		$first_name = $this->input->post('first_name');
		$email = $this->input->post('email');
		$token = $this->input->post('token');
		$Periode = strval(date("Y")).strval(date("m"));
		// Set your Merchant Server Key
		\Midtrans\Config::$serverKey = $this->ModelsExecuteMaster->midTransServerKey();
		// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
		\Midtrans\Config::$isProduction = $this->ModelsExecuteMaster->midTransProduction();
		// Set sanitization on (default)
		\Midtrans\Config::$isSanitized = true;
		// Set 3DS transaction for credit card to true
		\Midtrans\Config::$is3ds = true;
		
		$order_id = $Periode.strval(rand());
		$params = array(
		    'transaction_details' => array(
		        'order_id' => $order_id,
		        'gross_amount' => $amt + $Adminfee,
		    ),
		    'payment_type' => 'gopay',
		    'customer_details' => array(
		        'first_name' => $first_name,
		        'email' => $email
		    ),
		);
		 
		$response = \Midtrans\CoreApi::charge($params);

		$NoTransaksi = $response->order_id;
		$TglTransaksi = $response->transaction_time;
		$TglPencatatan = date("Y/m/d hh:mm:ss");
		$MetodePembayaran = $response->payment_type;
		$GrossAmt = $response->gross_amount;
		$AdminFee = $Adminfee;
		$Mid_PaymentType = $response->payment_type;
		$Mid_TransactionID = $response->transaction_id;
		$Mid_MechantID = $response->merchant_id;
		$Mid_Bank = "";
		$Mid_VANumber = "";
		$Mid_SignatureKey = "";
		$Mid_TransactionStatus = $response->transaction_status;
		$Mid_FraudStatus = $response->fraud_status;

		$param = array(
			'NoTransaksi' => $NoTransaksi,
			'TglTransaksi' => $TglTransaksi,
			'TglPencatatan' => $TglPencatatan,
			'MetodePembayaran' => $MetodePembayaran,
			'GrossAmt' => $GrossAmt,
			'AdminFee' => $AdminFee,
			'Mid_PaymentType' => $Mid_PaymentType,
			'Mid_TransactionID' => $Mid_TransactionID,
			'Mid_MechantID' => $Mid_MechantID,
			'Mid_Bank' => $Mid_Bank,
			'Mid_VANumber' => $Mid_VANumber,
			'Mid_SignatureKey' => $Mid_SignatureKey,
			'Mid_TransactionStatus' => $Mid_TransactionStatus,
			'Mid_FraudStatus' => $Mid_FraudStatus,
		);

		try {
			$exist = $this->ModelsExecuteMaster->FindData(array('NoTransaksi'=>$NoTransaksi),'topuppayment');
			if ($exist->num_rows() > 0) {
				$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('NoTransaksi'=>$NoTransaksi),'topuppayment');
			}
			else{
				$rs = $this->ModelsExecuteMaster->ExecInsert($param,'topuppayment');
			}
			if ($rs) {
				$data['success'] = true;
			}	
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
			$data['success'] = false;
		}
		$data['data'] = $response;
		echo json_encode($data);
	}

	public function testCharge(){
		\Midtrans\Config::$serverKey = $this->ModelsExecuteMaster->midTransServerKey();
		// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
		\Midtrans\Config::$isProduction = $this->ModelsExecuteMaster->midTransProduction();
		// Set sanitization on (default)
		\Midtrans\Config::$isSanitized = true;
		// Set 3DS transaction for credit card to true
		\Midtrans\Config::$is3ds = true;
		
		// $order_id = $Periode.strval(rand());
		$params = array(
		    'transaction_details' => array(
		        'order_id' => rand(),
		        'gross_amount' => 10000,
		    ),
		    'payment_type' => 'bank_transfer',
		    'customer_details' => array(
		        'first_name' => 'tampan',
		        'email' => 'tampan@tampan.com'
		    ),
		    'bank_transfer' => array(
		    	'bank' => 'bni'
		    )
		);
		 
		$response = \Midtrans\CoreApi::charge($params);
		var_dump($response);
	}
	public function konfirmasiPayment(){
		$data = array('success' => false ,'message'=>array(),'imageurl'=>array());
		$NoTransaksi = $this->input->post('NoTransaksi');
		$image = $this->input->post('baseimage');
		$imagename = $this->input->post('imagename');

		// var_dump($image.' ; '.$imagename);
		$temp = base64_decode($image);
		$link = 'localData/confirmation/'.$imagename;
		try {
			file_put_contents($link, $temp);	
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		$fulllink = base_url().$link;

		$rs = $this->ModelsExecuteMaster->ExecUpdate(array('Attachment'=>$fulllink),array('NoTransaksi'=> $NoTransaksi),'topuppayment');
		if ($rs) {
			$data['success'] = true;
			$data['imageurl'] = $fulllink;
		}
		else{
			$data['message'] = 'Gagal Update Password';
		}
		echo json_encode($data);
	}

	public function RequestPayment(){
		$data = array('success' => false ,'message'=>array(),'data' => array(),'url'=>'');

		$amt = $this->input->post('amt');
		$Adminfee = $this->input->post('Adminfee');
		$first_name = $this->input->post('first_name');
		$email = $this->input->post('email');
		$Periode = strval(date("Y")).strval(date("m"));
		$payment_type = $this->input->post('payment_type');
		$bank = $this->input->post('bank');
		$payment_method = $this->input->post('payment_method');
		// Set your Merchant Server Key
		\Midtrans\Config::$serverKey = $this->ModelsExecuteMaster->midTransServerKey();
		// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
		\Midtrans\Config::$isProduction = $this->ModelsExecuteMaster->midTransProduction();
		// Set sanitization on (default)
		\Midtrans\Config::$isSanitized = true;
		// Set 3DS transaction for credit card to true
		\Midtrans\Config::$is3ds = true;
		
		$order_id = $Periode.strval(rand());
		$params = array();
		if ($bank != '') {
			$params = array(
			    'transaction_details' => array(
			        'order_id' => $order_id,
			        'gross_amount' => round($amt + $Adminfee),
			    ),
			    'payment_type' => $payment_type,
			    'customer_details' => array(
			        'first_name' => $first_name,
			        'email' => $email
			    ),
			    'bank_transfer' => array(
			    	'bank' => $bank
			    )
			);
		}else{
			$params = array(
			    'transaction_details' => array(
			        'order_id' => $order_id,
			        'gross_amount' => round($amt + $Adminfee),
			    ),
			    'payment_type' => $payment_type,
			    'customer_details' => array(
			        'first_name' => $first_name,
			        'email' => $email
			    ),
			);
		}
		// var_dump($params);
		// if ($bank != '') {
		// 	$bankdetail = array(
		// 		'bank_transfer' => array(
		// 			'bank' => $bank
		// 		)
		// 	);
		// 	array_push($params, $bankdetail);
		// }
		// var_dump($params);
		$response = \Midtrans\CoreApi::charge($params);

		$Attachment = "";
		$NoTransaksi = $response->order_id;
		$TglTransaksi = $response->transaction_time;
		$TglPencatatan = date("Y-m-d h:i:sa");
		$MetodePembayaran =  $payment_method; //$response->payment_type;
		$GrossAmt = $response->gross_amount;
		$AdminFee = $Adminfee;
		$Mid_PaymentType = $response->payment_type;
		$Mid_TransactionID = $response->transaction_id;
		$Mid_MechantID = $response->merchant_id;
		if ($bank != '') {
			$Mid_Bank = $response->va_numbers[0]->bank;
			$Mid_VANumber = $response->va_numbers[0]->va_number;
		}
		else{
			$Mid_Bank = '';
			$Mid_VANumber = '';
		}
		$Mid_SignatureKey = "";
		$Mid_TransactionStatus = $response->transaction_status;
		$Mid_FraudStatus = $response->fraud_status;

		if ($bank == '') {
			$Attachment = $response->actions[0]->url;
			$data['url'] = $response->actions[0]->url;
		}
		elseif ($bank == 'gopay') {
			$Attachment = $response->actions[1]->url;
			$data['url'] = $response->actions[1]->url;
		}
		$param = array(
			'NoTransaksi' => $NoTransaksi,
			'TglTransaksi' => $TglTransaksi,
			'TglPencatatan' => $TglPencatatan,
			'MetodePembayaran' => $MetodePembayaran,
			'GrossAmt' => $GrossAmt,
			'AdminFee' => $AdminFee,
			'Mid_PaymentType' => $Mid_PaymentType,
			'Mid_TransactionID' => $Mid_TransactionID,
			'Mid_MechantID' => $Mid_MechantID,
			'Mid_Bank' => $Mid_Bank,
			'Mid_VANumber' => $Mid_VANumber,
			'Mid_SignatureKey' => $Mid_SignatureKey,
			'Mid_TransactionStatus' => $Mid_TransactionStatus,
			'Mid_FraudStatus' => $Mid_FraudStatus,
			'UserID'	=> $first_name,
			'Attachment' => $Attachment
		);

		try {
			$exist = $this->ModelsExecuteMaster->FindData(array('NoTransaksi'=>$NoTransaksi),'topuppayment');
			if ($exist->num_rows() > 0) {
				$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('NoTransaksi'=>$NoTransaksi),'topuppayment');
			}
			else{
				$rs = $this->ModelsExecuteMaster->ExecInsert($param,'topuppayment');
			}
			if ($rs) {
				$data['success'] = true;
			}	
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
			$data['success'] = false;
		}
		$data['data'] = $response;
		echo json_encode($data);
	}

}