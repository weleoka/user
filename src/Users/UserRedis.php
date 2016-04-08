<?php

namespace Weleoka\Users;
// How does extends work in php... does it call constructors?

/**
 * Class for users mapping to a redis database.
 *
 */
class UserRedis extends \Weleoka\Users\UsersdbModelRedis {


    /**
     * Constructor.
     */
    public function __construct ()
    {
        $this->redis = new \Predis\Client(array(
            "scheme"    => "tcp",
            "host"      => "127.0.0.1",
            "port"      => 6379,
            "password"  => "",
            "database"  => 10,
            "persistent" => "0"
        ));

        // Initial database set-up
        if (!$this->redis->exists('usercount')) {
            $this->redis->set("usercount", 0);
        }
        if (!$this->redis->exists('usersprefix')) {
            $this->redis->set("usersprefix", "userID:");
        }
        $this->redis->set("usersprefix", "userID:");
        $this->usersprefix = $this->getUsersPrefix();
    }


    /**
     * Get the usercount.
     *
     * @return int
     */
    public function getUserCount()
    {
        return $this->redis->get('usercount');
    }


    /**
     * Get the users prefix which is prepended to ID numbers.
     *
     * @return string
     */
    public function getUsersPrefix()
    {
        return $this->redis->get('usersprefix');
    }


    /**
     * Get user as array.
     *
     * @param int the user id to get.
     * @return array
     */
    public function getUser($id)
    {
        return $this->hgetall($this->usersprefix . $id);
    }


    /**
     * Get user as HTML.
     *
     * @param int $id the user id to get.
     * @return string
     */
    public function getUserHTML($id)
    {
        $arr = $this->getUser($id);
     
        return "<p>"
            . "<br>Active: "    . $arr['active']
            . "<br>Full name :" . $arr['fullname']
            . "<br>Username: "  . $arr['username']
            . "<br>email :"     . $arr['email']
            . "<br>profile :"   . $arr['profile']
            . "<br>created :"   . $arr['created']
            . "<br>latestIp :"  . $arr['latestIp']
            . "<br>firstIp :"   . $arr['firstIp']
            . "</p>";
    }




/* SEARCH and FIND METHODS **************************************/

    /**
     * Find and return user by username.
     *
     * @param string $username name to search for.
     *
     * @return int Identification number of user
     */
    public function findIDByUsername($username)
    {
        $userlist = $this->redis->hKeys('userlist');

        if (in_array($username, $userlist, True)) {
            // Note: If the search parameter is a string and the type parameter is set to TRUE, the search is case-sensitive. in_array(search,array,type)
            $this->addFeedback("USERNAME EXISTS.");

            return $this->hget("userlist", $username);
        }

        return null;
    }




/* SIGNUP, LOGIN AND LOGOUT METHODS **************************************/

    /**
     * Sign up new user.
     *
     * @param string $username, string $password.
     *
     * @return bool
     */
    public function signup($formFields)
    {
        $extraInfo = [
            'created'   => $this->getTime(),
            'active'    => $this->getTime(),
            'firstIp'   => $_SERVER['REMOTE_ADDR'],
            'latestIp'  => $_SERVER['REMOTE_ADDR'],
        ];
        $newUserData = array_merge($extraInfo, $formFields);

        $newUserID = $this->getUserCount();
        $key_user = $this->usersprefix . $newUserID;

        $username = $newUserData['username'];
        $password = $newUserData['password'];

        // Hash and salt the new password.
        $newUserData['password'] = \Sodium\crypto_pwhash_scryptsalsa208sha256_str(
            $password,
            \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE,
            \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE
        );

        // Step 1: Check username is not taken.
        if (!$this->findIDByUsername($username)) {
            // Step 2: Register the new username and corresponding ID in userlist.
            $res = $this->redis->hmset("userlist", [
                $username => $newUserID,
            ]);
            // Step 3: Create the new hash for the user.
            $res = $this->redis->hmset($key_user, $newUserData);
            // Step 4: Increment the usercount.
            $this->redis->incr("usercount");
        }
    }


    /**
     * Login using credentials.
     *
     * @param array $credentials.
     *
     * @return bool
     */
    public function login($credentials)
    {
        $currentUserID = $this->findIDByUsername($credentials['username']);
        
        if ($currentUserID) {
            $key_user = $this->usersprefix . $currentUserID; //$this->redis->hget("userlist", $username);
            
            $hash_str = $this->redis->hget($key_user, 'password');

            if (\Sodium\crypto_pwhash_scryptsalsa208sha256_str_verify($hash_str, $credentials['password'])) {
                \Sodium\memzero($credentials['password']);
                $this->addFeedback("LOGGED IN.");

                $_SESSION['user'] = [
                    'id'        => $currentUserID,
                    'username'  => $credentials['username']
                ];
                $this->sessionTimeoutRestart();

                return true;

            } else {
                \Sodium\memzero($credentials['password']);
                $this->addFeedback("FAILED LOG IN for " . $key_user);

                return false;
            }

        } else {
            // Run a fake to take time.
            $hash_str = $this->redis->hget("userID:0", 'password');
            \Sodium\crypto_pwhash_scryptsalsa208sha256_str_verify($hash_str, $password);
            // session_unset();
            $this->addFeedback("FAILED LOG IN.");

            return false;
        }
    }




/* Authentification check METHODS *****************************************/

    /**
     * Check if user is logged in.
     *
     * @return bool
     */
    public function isAuthenticated()
    {

        if(isset($_SESSION['user'])){

            return true;

        } else {

            return false;
        }
    }


    /**
     * Check if user is admin.
     * This method is called frequently for all actions,
     * and because of that it is also where Time To Live is checked.
     *
     * @return bool
     */
    public function isAdmin()
    {
        $username = $this->whoIsAuthenticated();
        // Check the Time to Live
        $this->sessionTimeout();
        // Reset the Time To Live
        $this->sessionTimeoutRestart();

        if (isset($username) && $username == 'admin') {

            return true;

        } else {

            return false;
        }
    }


    /**
     * Check if user is logged in and return string username.
     *
     * @return string $username Name of user signed in.
     */
    public function whoIsAuthenticated()
    {
        $username = isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : null;

        return $username;
    }


    /**
     * Redirect user who access controller actions without authentification.
     *
     * @return void
     */
    public function kickOutBaddie()
    {
        $this->addFeedback('Du är inte inloggad.');
        // header("Location: " . $_SERVER['PHP_SELF']);
        header('Refresh: 3; URL=' . ROO_INSTALL_PATH .'/webroot/index.php');
    }




/* TTL TIMEOUT METHODS *****************************************/

    /**
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


    /**
     * Set session timeout to $_SESSION, check status of TTL.
     *
     * @return username
     */
    public function sessionTimeout()
    {
        // check to see if $_SESSION["timeout"] is set
        if (isset($_SESSION["timeout"])) {
            // calculate the session's "time to live"
            $currentTTL = time() - $_SESSION['timeout']['startPoint'];

            if ($currentTTL > $_SESSION['timeout']['TTL']) {
                session_unset();
                $this->addFeedback('Du har varit inaktiv i 10 minuter och numera utloggad.');
            }
        }
    }




/* USERS CLASS FEEDBACK METHODS *****************************************/

    /**
     * Add output to display to the user what happened.
     *
     * @param string $str the string to add as output.
     *
     * @return void.
     */
    public function addFeedback($str)
    {
        if (isset($str)) {
            $_SESSION['user-feedback'] = $str;

        } else {
            $_SESSION['user-feedback'] = null;
        }
    }


    /**
     * Get output to display to the user what happened.
     *
     * @param void
     *
     * @return string
     */
    public function getFeedback()
    {

        return isset($_SESSION['user-feedback'])
            ? $_SESSION['user-feedback']
            : " ";
    }
}
