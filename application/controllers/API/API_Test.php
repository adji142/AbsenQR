<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Test extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
		$this->load->model('LoginMod');
		require_once(APPPATH.'libraries/midtrans/Midtrans.php');
	}

	public function Test()
	{
		// $this->db->query("insert into testCron values(now())");
		define( 'API_ACCESS_KEY', 'AAAAWRnKigc:APA91bF2DUbxrbIws3clI_lGq40MMbc0x9hjYZjf6xyTGukNVb8BrgIWYTMnz6NB2-ZdGYpVSo2UuKjz3YaVcN777aIU-dGNdTdEYKRtRwYMF0s8gJu5oPLg8zoivTAPQf_pZASw0w4A' );

		\Midtrans\Config::$isProduction = $this->ModelsExecuteMaster->midTransProduction();
		\Midtrans\Config::$serverKey = $this->ModelsExecuteMaster->midTransServerKey();
		$notif = new \Midtrans\Notification();
		// $this->db->query("insert into testCron values('".$notif->order_id."')");

		// echo "hayy saya dapat : ".$order_id;
		$order_id = $notif->order_id;
		// $order_id = '2021081204799914';
		$SQL = "
			SELECT b.token,a.Mid_TransactionStatus FROM topuppayment a 
			INNER JOIN users b on a.UserID = b.username
			WHERE a.NoTransaksi = '".$order_id."'
		";
		
		$rs = $this->db->query($SQL);
		if ($rs) {
			// var_dump($rs->row());
			$registrationIds = array($rs->row()->token);

			// prep the bundle
			if ($rs->row()->Mid_TransactionStatus != 'settlement') {
				$msg = array
				(
				    'message'   => 'Permintaan Pembayaran anda sudah kami terima', //
				    'title'     => 'SpiritBooks#Proses Pembayaran',
				    'subtitle'  => 'This is a subtitle. subtitle',
				    'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
				    'vibrate'   => 1,
				    'sound'     => 1,
				    'largeIcon' => 'large_icon',
				    'smallIcon' => 'small_icon'
				);
			}
			else{
				$msg = array
				(
				    'message'   => 'Pembayran anda berhasil terkonfirmasi', // 
				    'title'     => 'SpiritBooks#Pembayaran Berhasil',
				    'subtitle'  => 'This is a subtitle. subtitle',
				    'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
				    'vibrate'   => 1,
				    'sound'     => 1,
				    'largeIcon' => 'large_icon',
				    'smallIcon' => 'small_icon'
				);
			}
			$fields = array
			(
			    'registration_ids'  => $registrationIds,
			    'data'          => $msg
			);
			  
			$headers = array
			(
			    'Authorization: key=' . API_ACCESS_KEY,
			    'Content-Type: application/json'
			);
			  
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			echo $result;	
		}
	}


	public function TestNotif()
	{
		$notification = array("condition"=>"'SpiritBooksNotification' in topics","notification"=>array());
		$notification['notification'] = array(
			"title" => "temanmesra.zyz",
			"body" => "Bohay"
		);
		// echo json_encode($data);
		$this->ModelsExecuteMaster->PushNotification($data);
	}

	public function testNumber()
	{
		$Kolom = 'KodeCustomer';
		$Table = 'masterpelanggan';
		$Prefix = 'CL';

		$KodeCustomer = '';
		$SQL = "SELECT RIGHT(MAX(".$Kolom."),5)  AS Total FROM " . $Table . " WHERE LEFT(" . $Kolom . ", LENGTH('".$Prefix."')) = '".$Prefix."'";

		// var_dump($SQL);
		$rs = $this->db->query($SQL);

		$temp = $rs->row()->Total + 1;

		$nomor = $Prefix.str_pad($temp, 5,"0",STR_PAD_LEFT);
		if ($nomor != '' && $KodeCustomer == '') {
			$KodeCustomer = $nomor;
		}

		echo $KodeCustomer;
	}
	public function testGlobal()
	{
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=-7.4949406,110.8311577&sensor=false&key=AIzaSyCG7FscIk67I9yY_fiyLc7-_1Aoyerf96E';
    	$data = @file_get_contents($url);
    	$output = json_decode($data,true);
    	$data = array();
    	// echo json_encode($output);
    	for($j=0;$j<count($output['results'][0]['address_components']);$j++){
	        echo '<b>'.$output['results'][0]['address_components'][$j]['types'][0]	.': </b>  '.$output['results'][0]['address_components'][$j]['long_name'].'<br/>';
	        $dem_name = "";
	        switch ($output['results'][0]['address_components'][$j]['types'][0]) {
	        	case "administrative_area_level_1":
				    $dem_name = 'Provinsi';
				    break;
				case "administrative_area_level_2":
				    $dem_name = 'Kota';
				    break;
				case "administrative_area_level_3":
				    $dem_name = 'Kecamatan';
				    break;
				case "administrative_area_level_4":
				    $dem_name = 'Kelurahan';
				    break;
				case "route":
				    $dem_name = 'Alamat';
				    break;
				case "street_number":
				    $dem_name = 'No.Rumah';
				    break;
				case "postal_code":
				    $dem_name = 'postal_code';
				    break;
				case "country":
				    $dem_name = 'country';
				    break;
				default:
					$dem_name = "";
	        }
	        $tempname1 = str_replace('Kelurahan ', '', $output['results'][0]['address_components'][$j]['long_name']);
	        $tempname2 = str_replace('Kecamatan ', '', $tempname1);
	        $tempname3 = str_replace('Kota ', '', $tempname2);
	        $tempname4 = str_replace(' Regency', '', $tempname3);
	        $tempname5 = str_replace('Central Java', 'Jawa Tengah', $tempname4);
	        $tempname6 = str_replace('West Java', 'Jawa Barat', $tempname5);
	        $tempname7 = str_replace('East Java', 'Jawa Timur', $tempname6);
	        $temp = array(
	        	'Dem' => $dem_name,
	        	'Name' => $tempname7
	        );
	        array_push($data, $temp);
	    }
	    // for ($i=0; $i < count($data) ; $i++) { 
	    // 	echo $data[1];
	    // }
	    $lastResult = array();
	    foreach ($data as $key) {
	    	// echo $key['Dem'];
	    	switch ($key['Dem']) {
	    		case "Provinsi":
					$Q_CekProv = "SELECT * FROM dem_provinsi where prov_name like '%".$key['Name']."%'";
					$rs = $this->db->query($Q_CekProv);
					if ($rs->num_rows() > 0) {
						$temp = array(
							'Dem' 	=> $key['Dem'],
							'id'	=> $rs->row()->prov_id,
							'name'	=> $rs->row()->prov_name
						);
						array_push($lastResult, $temp);
					}
				    break;
				case "Kota":
					$Q_CekProv = "SELECT * FROM dem_kota where city_name like '%".$key['Name']."%'";
					$rs = $this->db->query($Q_CekProv);
					if ($rs->num_rows() > 0) {
						$temp = array(
							'Dem' 	=> $key['Dem'],
							'id'	=> $rs->row()->city_id,
							'name'	=> $rs->row()->city_name
						);
						array_push($lastResult, $temp);
					}
				    break;
				case "Kelurahan":
					$Q_CekProv = "SELECT * FROM dem_kelurahan where subdis_name like '%".$key['Name']."%'";
					$rs = $this->db->query($Q_CekProv);
					if ($rs->num_rows() > 0) {
						$temp = array(
							'Dem' 	=> $key['Dem'],
							'id'	=> $rs->row()->subdis_id,
							'name'	=> $rs->row()->subdis_name
						);
						array_push($lastResult, $temp);
					}
				    break;
				case "Kecamatan":
					$Q_CekProv = "SELECT * FROM dem_kecamatan where dis_name like '%".$key['Name']."%'";
					$rs = $this->db->query($Q_CekProv);
					if ($rs->num_rows() > 0) {
						$temp = array(
							'Dem' 	=> $key['Dem'],
							'id'	=> $rs->row()->dis_id,
							'name'	=> $rs->row()->dis_name
						);
						array_push($lastResult, $temp);
					}
				    break;
				case "postal_code":
					$temp = array(
						'Dem' 	=> $key['Dem'],
						'id'	=> 0,
						'name'	=> $key['Name']
					);
					array_push($lastResult, $temp);
				    break;
				case "Alamat":
						$temp = array(
							'Dem' 	=> $key['Dem'],
							'id'	=> 0,
							'name'	=> $key['Name']
						);
						array_push($lastResult, $temp);
					    break;
	    	}
	    }
	    // var_dump($data);
	    echo json_encode($lastResult);
	    // var_dump($output['results'][0]['address_components']);
	}
}