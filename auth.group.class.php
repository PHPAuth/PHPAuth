<?php
/**
 * Auth group extension class
 * @author Mooztik
 */
/**
 * super admin rights is 99 for each group
 * user added to the default admin group with the 99 level have full access 
 * admin group is a system group who should'nt be deleted
 * user added to any other group with 99 have group control (can change name and description, but no right to delete this group)
 */
 
 class AuthGroup extends Auth
 {
 		
	private $dbh;
    private $config;
	public $uid;
	public $userdata;
	
   /*
    * Initiates database connection
    * link to parent class
    */
   
	public function __construct(\PDO $dbh, $config)
    {
    	parent::__construct($dbh, $config);
		$this->config = $config;
        $this->dbh = $dbh;
    }
	
	
	/**
	 * Populate $userdata var
	 * without sensible datas
	 */
	 
	 public function getUserdata()
	 {
        $query = $this->dbh->prepare("SELECT username  FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($this->uid));

        if ($query->rowCount() == 0) {
            return false;
        } else {
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return false;
            } else {
                $data['uid'] = $this->uid;

                return $data;
            }
        }
	 }
	
	
	/**
	 * check if user is in this group to let him in
	 * @param int $gid : group id
	 * @return boolean
	 */
	public function checkAuthGroup($gid)
	{
		//prepare query
		$query = $this->dbh->prepare("SELECT uid FROM {$this->config->table_usergroups} WHERE gid = ? AND uid = ?");
        $query->execute(array($gid, $this->uid));

        if ($query->rowCount() == 0) {
            return false;
        } else {
        	return true;
		}
	}
	
	/**
	 * get datas from one group
	 * @param int $gid : group id
	 * @param bool $full : get users list if true
	 * @param var $groupName : get group data by name (usefull for name search)
	 * @return array
	 */
	public function getGroup($gid, $full = false, $groupName = false)
	{
		$data = array();		
		
		if($gid !== 0) 
		{
			$query = $this->dbh->prepare("SELECT * FROM {$this->config->table_groups} WHERE gid = ?");
	        $query->execute($gid);
		} elseif ($groupName !== false) {
			$query = $this->dbh->prepare("SELECT * FROM {$this->config->table_groups} WHERE group_name LIKE '%?%'");
	        $query->execute($groupName);
		} else {
			return false;
		}
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return false;
            } else {
                if($full === TRUE)
				{
					$data['users'] =  $this->groupUsersGet($gid);
				}
						
				return $data;
            }
        }
	}
	
	/**
	 * create or modify group
	 * @param int $gid  : group id, if set -> modify
	 * @param var $groupName
	 * @param var $groupDescription
	 * @return bool
	 */
	public function setGroup($gid, $groupName, $groupDescription)
	{
		// no group id, then create a new group
		if(!isset($gid)) {
			return $this->groupCreate($group_name, $group_desc);
		}
		else { // modify group information
			return $this->groupModify($gid, $group_name, $group_desc);
		}
			
	}
	
	/**
	 * add, remove or change admin level from one user from group(s)
	 * if $gid is an array, user will be removed from multiple groups
	 */
	public function SetUserInGoup($gid, $uid, $set)
	{
		if(is_array($uid)) {			
			return false;
		}	
			
		if($set === 'add') {
			return $this->groupUserAdd($gid, $uid);
		} elseif ($set === 'delete') {
			return $this->groupUserDelete($gid, $uid);
		} else {
			return false;
		}
	}
	
	/**
	 * Get user list from group(s)
	 */
	public function getUserByGroup($gid)
	{
		$data = array();
		
		$query = $this->dbh->prepare("SELECT ug.uid, ug.level, u.username FROM {$this->config->table_usergroups} as ug LEFT JOIN {$this->config->table_users} as u ON u.id = ug.uid WHERE ug.gid=?");
        $query->execute(array($gid));
		
		if ($query->rowCount() == 0) {
            return false;
        } else {
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return false;
            } else {
            	return $data;
			}
		}
	}
	
	/**
	 * get groups from one user
	 */
	public function getGroupByUser($uid)
	{
		$data = array();
		
		$query = $this->dbh->prepare("SELECT g.*, ug.level FROM {$this->config->table_groups} AS g INNER JOIN {$this->config->table_usergroups} AS ug ON ug.gid = g.gid WHERE ug.uid=?");
        $query->execute(array($uid));
		
		if ($query->rowCount() == 0) {
            return false;
        } else {
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return false;
            } else {
            	return $data;
			}
		}
	}
	
	
	
	// Go for private internal secret functions
	
	// add group to DB
	private function groupCreate($group_name, $group_desc)
	{
		// first check if this group name does not already exists 
		if($this->groupGet(false, false, $group_name) !== FALSE) {
			return false;
		}
		
		// security check on group name
		if(!validateGroupName($groupName)) {
			return false;
		}
		
		// security check on group description
		$group_desc = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $group_desc), ENT_COMPAT, 'UTF-8'));
		if(empty($group_desc)) {
			return false;
		}
		
		 $query = $this->dbh->prepare("INSERT INTO {$this->config->table_groups} (group_name, group_desc) VALUES (?, ?)");
         $return = $query->execute(array(               
                $group_name,
                $group_desc));

         return true;	
	}
	
	// modify group into DB
	private function groupModify($gid, $group_name, $group_desc)
	{
		// if group id is not integrer, it s an error
		if(!is_int($gid)){
			return false;
		}
		
		// security check on group name
		if(!validateGroupName($groupName)) {
			return false;
		}
		
		// security check on group description
		$group_desc = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $group_desc), ENT_COMPAT, 'UTF-8'));
		if(empty($group_desc)) {
			return false;
		}
		
		 $query = $this->dbh->prepare("UPDATE {$this->config->table_groups} SET group_name = ?, group_desc = ? WHERE gid = ?");
         $return = $query->execute(array(               
                $group_name,
                $group_desc,
                $gid));

         return true;	
	}
	
	// delete group from DB
	private function groupDelete($gid)
	{
		// if group id is not integrer, it s an error
		if(!is_int($gid)){
			return false;
		}
		// first delete users from this group
		$query = $this->dbh->prepare("DELETE FROM {$this->config->table_usergroups} WHERE gid = ?");
		$return = $query->execute(array($gid));
		//then delete the group
		$query = $this->dbh->prepare("DELETE FROM {$this->config->table_groups} WHERE gid = ?");
		$return = $query->execute(array($gid));
		
	}

	//add user(s) to group
	private function groupUserAdd($gid, $uid, $admin = false)
	{
		// if group or user id is not integrer, it s an error
		if(!is_int($gid) || !is_int($uid)){
			return false;
		}
		
		//check if user is not already in group
		$data = array();
		
		$query = $this->dbh->prepare("SELECT * FROM {$this->config->table_usergroups} WHERE gid = ? AND uid=?");
        $query->execute(array($gid, $uid));
		
		if ($query->rowCount() != 0) {
            return false;
        }
		
		 $query = $this->dbh->prepare("INSERT INTO {$this->config->table_usergroups} (gid, uid, admin) VALUES (?, ?, ?)");
         $return = $query->execute(array(               
                $gid,
                $uid,
				$admin));

         return true;	
		
	}
	
	// remove user(s) from group
	private function groupUserDelete($gid, $uid, $admin = false)
	{
		// if group or user id is not integrer, it s an error
		if(!is_int($gid) || !is_int($uid)){
			return false;
		}
		$query = $this->dbh->prepare("DELETE FROM {$this->config->table_usergroups} WHERE gid = ? AND uid = ?");
		$return = $query->execute(array($gid, $uid));
		
		return true;
	}
	
	
	// change admin level
	private function groupUserAdmin($gid, $uid, $admin = false)
	{
		// if group or user id is not integrer, it s an error
		if(!is_int($gid) || !is_int($uid)){
			return false;
		}
		
		$query = $this->dbh->prepare("UPDATE {$this->config->table_usergroups} SET admin = ? WHERE gid = ? AND uid = ?");
		$return = $query->execute(array($admin, $gid, $uid));
		
		return true;
		
	}
	
	
	
	/*
    * Verifies that a username is valid
    * @param string $username
    * @return array $return
    */
    private function validateGroupName($groupName) 
    {
        if (strlen($groupName) < 3) {
            $this->return['message'][] = "group_name_short";
            $this->addNewLog(0, "GROUP_NAME_SHORT", "Group name : {$groupName}");
        } elseif (strlen($groupName) > 30) {
            $this->return['message'][] = "group_name_long";
            $this->addNewLog(0, "GROUP_NAME_LONG", "Group name : {$groupName}");
        } elseif (!ctype_alnum($groupName)) {
            $this->return['message'][] = "group_name_invalid";
            $this->addNewLog(0, "GROUP_NAME_INVALID", "Group name : {$groupName}");
        } else {
            return true;
        }
		return false;
    } 


}
?>
