<?php
namespace Weleoka\Users;
 
/**
 * Model for Users.
 *
 */
class User extends \Weleoka\Users\UsersdbModel {


    /**
     * Add output to display to the user what happened whith the form.
     *
     * @param string $str the string to add as output.
     *
     * @return $this CForm.
     */
    public function AddFeedback($str)
    {
        if (isset($str)) {
            $_SESSION['user-feedback'] =  $str;
        } else {
            $_SESSION['user-feedback'] = null;
        }
        return $this;
    }
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
   		$this->session->set('user', $user->name);
      	return true;
   	 } else {
   	   $this->session->un_set('user');
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
}