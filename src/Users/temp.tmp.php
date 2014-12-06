<?php

/* 
/*
 	public function __construct($db) {
 		$this->db=$db;
 	}

/*
 * login
 *
 */
	public function login($user,$password) {
		$sql = "SELECT acronym, name FROM User WHERE acronym = ? AND password = md5(concat(?, salt))";
		$params = array();
		$params=[htmlentities($user),  htmlentities($password)];
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);

		if(isset($res[0])) {
			$_SESSION['user'] = $res[0];
			return true;
		} else {
			return false;
		}
	}


	public function findAll()
	{
	  	$this->db->select()->from($this->getSource());
     	$this->db->execute();
     	return $this->db->fetchInfo($this);	
	}
	
	

    
    ---------- check password ----------------
    if(password_verify($password, $res->password)){
                    return $res;
                }else{
                    return false;
                }
*/ 
*/
*/
