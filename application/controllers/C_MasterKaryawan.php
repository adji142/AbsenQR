<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_MasterKaryawan extends CI_Controller {

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

		$Nik = $this->input->post('Nik');

		$SQL = "
			SELECT a.*, b.NamaJabtan, c.NamaDepartement,
			CASE WHEN a.Active = 1 THEN 'Aktif' ELSE 'InAktif' END StatusKaryawan
			FROM masterkaryawan a
			LEFT JOIN tjabatan b on a.KodeJabatan = b.KodeJabatan
			LEFT JOIN tdepartement c on KodeDepartement = c.KodeDepartment
			where 1 = 1
		";

		if ($Nik != '') {
			$SQL .= " AND a.Nik = '$Nik' ";
		}

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

	public function CRUD()
	{
		$data = array('success' => false ,'message'=>array());

		$Nik 				= $this->input->post('Nik');
		$NamaKaryawan 		= $this->input->post('NamaKaryawan');
		$KodeJabatan 		= $this->input->post('KodeJabatan');
		$KodeDepartment 	= $this->input->post('KodeDepartment');
		$Active 			= $this->input->post('Active');

		$ImageLink 	= $this->input->post('ImageLink');
		$ImageBase64 	= $this->input->post('picture_base64');

		$ArticleTable 	= 'masterkaryawan';
		// $exploder = explode("|",$ItemGroup[0]);
		$formtype = $this->input->post('formtype');

		$picture_ext = '';

		// Upload Image
		try {
			unset($config); 
			$date = date("ymd");
	        $config['upload_path'] = './localData/Karyawan';
	        $config['max_size'] = '60000';
	        $config['allowed_types'] = 'png|jpg|jpeg|gif';
	        $config['overwrite'] = TRUE;
	        $config['remove_spaces'] = TRUE;
	        $config['file_ext_tolower'] = TRUE;
	        $config['file_name'] = strtolower(str_replace(' ', '', $Nik));

	        $this->load->library('upload', $config);
	        $this->upload->initialize($config);

	        if(!$this->upload->do_upload('Attachment')) {
	        	if ($formtype == 'edit' || $formtype == 'delete' || $formtype == 'Publish') {
	        		$x='';
	        	}
	        	else{
	        		$x = $this->upload->data();
		        	// var_dump($x);
		        	$data['success'] = false;
		            $data['message'] = $this->upload->display_errors();
		            goto jumpx;
	        	}
	        }else{
	            $dataDetails = $this->upload->data();
	            $picture_ext = $dataDetails['file_ext'];
	            if ($picture_ext == '.jpeg') {
	            	$picture_ext = '.jpg';
	            }
	        }	
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = $e->getMessage();
			goto jumpx;
		}

		if ($ImageBase64 != '') {
			if ($formtype == 'add' || $formtype == 'edit') {
				$pos  = strpos($ImageBase64, ';');
				$type = explode(':', substr($ImageBase64, 0, $pos))[1];
				$extension = explode('/', $type)[1];
			}
			if ($extension == 'jpeg') {
				$picture_ext = '.jpg';
			}
		}

		$param = array(
			'Nik' 				=> $Nik,
			'NamaKaryawan'		=> $NamaKaryawan,
			'KodeJabatan'		=> $KodeJabatan,
			'KodeDepartement'	=> $KodeDepartment,
			'Active'			=> $Active,
			'ImageLink'		=> base_url().'localData/Karyawan/'.strtolower(str_replace(' ', '', $Nik)).''.strtolower($picture_ext),
			'ImageBase64'	=> $ImageBase64
		);

		if ($formtype == 'add') {
			$this->db->trans_begin();
			try {
				$call_x = $this->ModelsExecuteMaster->ExecInsert($param,$ArticleTable);
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
				$rs = $this->ModelsExecuteMaster->ExecUpdate($param,array('Nik'=> $Nik),$ArticleTable);
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
}