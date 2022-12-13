<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Demografi extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('ModelsExecuteMaster');
		$this->load->model('GlobalVar');
		$this->load->model('Apps_mod');
	}

	public function ReadProfinsi()
	{
		$MobileToken = $this->input->post('MobileToken');
		$Kriteria = $this->input->post('Kriteria');
		$data =array();
		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL ="SELECT prov_id AS ID, prov_name AS Title FROM dem_provinsi WHERE 1 = 1";
			if ($Kriteria != "") {
				$SQL .= " AND prov_name LIKE '%".$Kriteria."%'";
			}
			$SQL .= " ORDER BY prov_id";
			$rs = $this->db->query($SQL);
			$data = $rs->result();
		}
		echo json_encode($data);
	}

	public function ReadKota()
	{
		$MobileToken = $this->input->post('MobileToken');
		$ProvID = $this->input->post('ProvID');
		$Kriteria = $this->input->post('Kriteria');
		$data =array();
		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL ="SELECT city_id AS ID, city_name AS Title FROM dem_kota WHERE prov_id ='".$ProvID."' ";
			if ($Kriteria != "") {
				$SQL .= " AND city_name LIKE '%".$Kriteria."%'";
			}
			$SQL .= " ORDER BY city_id";
			$rs = $this->db->query($SQL);
			$data = $rs->result();
		}
		echo json_encode($data);
	}
	public function ReadKecamatan()
	{
		$MobileToken = $this->input->post('MobileToken');
		$KotaID = $this->input->post('KotaID');
		$Kriteria = $this->input->post('Kriteria');
		$data =array();
		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL ="SELECT dis_id AS ID, dis_name AS Title FROM dem_kecamatan WHERE city_id ='".$KotaID."' ";
			if ($Kriteria != "") {
				$SQL .= " AND dis_name LIKE '%".$Kriteria."%'";
			}
			$SQL .= " ORDER BY dis_id";
			$rs = $this->db->query($SQL);
			$data = $rs->result();
		}
		echo json_encode($data);
	}
	public function ReadKelurahan()
	{
		$MobileToken = $this->input->post('MobileToken');
		$KecID = $this->input->post('KecID');
		$Kriteria = $this->input->post('Kriteria');
		$data =array();
		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$SQL ="SELECT subdis_id AS ID, subdis_name AS Title FROM dem_kelurahan WHERE dis_id ='".$KecID."' ";
			if ($Kriteria != "") {
				$SQL .= " AND subdis_name LIKE '%".$Kriteria."%'";
			}
			$SQL .= " ORDER BY subdis_id";
			$rs = $this->db->query($SQL);
			$data = $rs->result();
		}
		echo json_encode($data);
	}

	public function VerificationAddress()
	{
		$MobileToken = $this->input->post('MobileToken');
		$LatLng = $this->input->post('LatLng');

		$lastResult = array();

		if ($this->ModelsExecuteMaster->GetToken($MobileToken)) {
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$LatLng.'&sensor=false&key=AIzaSyCG7FscIk67I9yY_fiyLc7-_1Aoyerf96E';
	    	$data = @file_get_contents($url);
	    	$output = json_decode($data,true);
	    	$data = array();
	    	// echo json_encode($output);
	    	for($j=0;$j<count($output['results'][0]['address_components']);$j++){
		        // echo '<b>'.$output['results'][0]['address_components'][$j]['types'][0]	.': </b>  '.$output['results'][0]['address_components'][$j]['long_name'].'<br/>';
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
		    // var_dump($output['results'][0]['address_components']);
		}
		echo json_encode($lastResult);
	}
}