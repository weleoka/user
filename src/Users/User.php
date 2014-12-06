<?php
namespace Weleoka\Users;
 
/**
 * Model for Users.
 *
 */
class User extends \Weleoka\Users\UsersdbModel {


/**
 * Find and return user by acronym.
 *
 * @return user
 */
	public function findByName( $acronym ) {
		echo "searching user: '" . $acronym . "'....";     
      $this->db->select()->from($this->getSource())->where('acronym = ?');
      $this->db->execute([$acronym]);
      $user = $this->db->fetchInto($this);
      
      return $user; 
    }
/*
 * Login user if password correct.
 *
 */
   public function loginUser ($acronym, $password) {
		$user = $this->findByName($acronym);
   	 
   	if ($user->password === crypt($password, $user->password)) {
   		$_SESSION['user'] = $user;
      	return true;
  
   	 } else {
   	 	$_SESSION['user'] = null;
			return false;
   	 }
   }

/*
 * Check if user is logged in.
 *
 */
	public function isAuthenticated() {
		if(isset($_SESSION['user'])){
			return true;
		} else {
			return false;
		}
	}
	
/*
 * Check if user is logged in and return Acronym.
 *
 * @return acronym
 */
	public function statusIsAuthenticated() {
		$acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

      return $acronym;
	}

/*
 * logout
 *
 */
	public function logout(){
		unset($_SESSION['user']);
	}
}