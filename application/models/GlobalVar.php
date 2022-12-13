<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class GlobalVar extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
	}
	function GetSideBar($userid,$parent,$usemobile)
	{
		$data = "
			select d.* from users a
			inner join userrole b on a.id = b.userid
			inner join permissionrole c on b.roleid = c.roleid
			inner join permission d on c.permissionid = d.id
			where a.id = $userid 
		";
		if ($usemobile == 1) {
			$data .= "AND d.AllowMobile = 1";
		}
		else{
			$data .= "AND d.menusubmenu = $parent";
		}
		$data .= "
			AND `status` = 1
			order by d.order asc
		";
		return $this->db->query($data);
	}
}