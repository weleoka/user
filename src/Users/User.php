<?php
namespace Weleoka\Users;

/**
 * Model for Users.
 *
 */
class User extends \Weleoka\Users\UsersdbModel {


/**
 * Add output to display to the user what happened.
 *
 * @param string $str the string to add as output.
 *
 * @return void.
 */
    public function AddFeedback($str)
    {
        if (isset($str)) {
            $_SESSION['user-feedback'] = $str;
        } else {
            $_SESSION['user-feedback'] = null;
        }
    }


/**
 * Find and return user by acronym.
 *
 * @param string $acronym name to search for.
 *
 * @return user
 */
	public function findByName( $acronym )
	{
		echo "searching user: '" . $acronym . "'....";
      $this->db->select()
      			->from($this->getSource())
      			->where('acronym = ?');
      $this->db->execute([$acronym]);
      $user = $this->db->fetchInto($this);

      return $user;
   }



/*
 * Login user if password correct.
 *
 * @param string $acronym, string $password.
 *
 * @return boolean
 */
   public function loginUser ($acronym, $password)
   {
		$currentUser = $this->findByName($acronym);

   	if ($currentUser->password === crypt($password, $currentUser->password)) {

   		$this->session->set('user', [
   												'id' 			=> $currentUser->id,
													'acronym' 	=> $currentUser->acronym,
													'name' 		=> $currentUser->name,
													'email' 		=> $currentUser->email,
													'contributionCount' => $currentUser->contributionCount,
												 ]);
      	return true;
   	 } else {
   	  session_unset();
			return false;
   	 }
   }



/*
 * Check if user is logged in.
 *
 */
	public function isAuthenticated()
	{
		if(isset($_SESSION['user'])){
			return true;
		} else {
			return false;
		}
	}



/*
 * Check if user is admin.
 *
 * @return acronym
 */
	public function isAdmin()
	{
		$acronym = $this->whoIsAuthenticated();
		$this->sessionTimeout();		
		
		if (isset($acronym) && $acronym == 'admin') {
			return true;
		} else {
			return false;
		}
	}



/*
 * Check if user is logged in and return string acronym.
 *
 * @return acronym
 */
	public function whoIsAuthenticated()
	{
		$acronym = isset($_SESSION['user']['acronym']) ? $_SESSION['user']['acronym'] : null;
      return $acronym;
	}



/************************************** FIND FORUM CONTENT BY USER ************************************/
/*
 * List any forum questions of user.
 *
 * @return array 
 */
	public function findUserQuestions($id) 
	{
		$this->db->select()
             	->from('Question')
             	->where('userID = ?');
      $this->db->execute($this->db->getSQL(), [$id]);
    	$this->db->setFetchModeClass(__CLASS__);	
    	$userQuestions = $this->db->fetchAll();
    	return object_to_array($userQuestions);
	}


	
/*
 * List any forum answers of user.
 *
 * @return array 
 */
	public function findUserAnswers($id) 
	{
		$this->db->select()
             	->from('Answer')
             	->where('userID = ?');
      $this->db->execute($this->db->getSQL(), [$id]);
    	$this->db->setFetchModeClass(__CLASS__);
		$userAnswers = $this->db->fetchAll();
    	return object_to_array($userAnswers);
	}



/************************************** TTL TIMEOUT FUNCTIONS *****************************************/
/*
 * Restart session TTL timeout.
 * Default TTL is 600 seconds.
 *
 * @return void
 */  
	public function sessionTimeoutRestart () 
	{
		$_SESSION['timeout']['startPoint'] = time();
		$_SESSION['timeout']['TTL'] = 600;	
	}   



/*
 * Set session timeout to $_SESSION, check status of TTL.
 *
 * @return acronym
 */
	public function sessionTimeout()
	{
			// check to see if $_SESSION["timeout"] is set
		if (isset($_SESSION["timeout"])) {
    		// calculate the session's "time to live"
  			$currentTTL = time() - $_SESSION['timeout']['startPoint'];
  			if ($currentTTL > $_SESSION['timeout']['TTL']) {
     			session_unset();
     			$this->AddFeedback('Du har varit inaktiv i 10 minuter och numera utloggad.');
  			}
		} 
	}

}


