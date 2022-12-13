<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class profileMod extends CI_Model
{
	function __construct()
    {
        parent::__construct();
    }
    public function getProfile($userID)
    {
    	$sql = "";
    	$sql = "
    		SELECT 
				a.id,
				b.id profileid,
				COALESCE(b.Email,a.email) Email,
				COALESCE(b.NoTlp,a.phone) Phone,
				a.username,
				b.FirstName,
				b.LastName,
				b.FullName,
				b.Birthday,
				b.Gender,
				b.provid,
				b.provname,
				b.kotaid,
				b.kotaname,	
				b.kecid,
				b.kecname,
				b.kelid,
				b.kelname,
				b.Address,
				b.GoogleToken,
				b.FBToken
			FROM users a
			LEFT JOIN pageprofile b on a.id = b.Userid 
    	";
    	if ($userID != '') {
    		$sql .= "Where a.id = $userID";
    	}
    	return $this->db->query($sql);
    }
}