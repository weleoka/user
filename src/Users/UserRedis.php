<?php

namespace Weleoka\Users;

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
        $this->redis = new Predis\Client(array(
            "scheme" => "tcp",
            "host" => "127.0.0.1",
            "port" => 6379,
            "password" => "",
            "database" => 10,
            "persistent" => "0"
        ));

        // Initial database set-up
        !$this->redis->exists('usercount') ? $this->redis->set("usercount", 0) : $res .= " (usercount: " . $this->redis->get("usercount") . ").";
    }


    /**
     * Get the usercount.
     *
     * @return int
     */
    public function getUsercount()
    {
        return $this->redis->getUsercount("usercount");
    }


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
     * Find and return user by username.
     *
     * @param string $username name to search for.
     *
     * @return int Identification number of user
     */
    public function findIDByUsername($username)
    {
        $userlist = $this->redis->hKeys('userlist')

        if (in_array($username, $userlist, True)) {
            // Note: If the search parameter is a string and the type parameter is set to TRUE, the search is case-sensitive. in_array(search,array,type)
            $this->addFeedback("USERNAME EXISTS.");

            return $username[$username];
            // $this->redis->hget("userlist", $username); 
        }

        return null;
    }


    /**
     * Sign up new user.
     *
     * @param string $username, string $password.
     *
     * @return bool
     */
    public function signup ()
    {
        $username = $_POST['username'];
        $fullname = $_POST['username'];
        $email = $_POST['username'];
        $password = $_POST['password'];
        $profile = $_POST['username'];

        $newUserID = $this->getUsercount();
        $key_user = "userID:" . $newUserID;

        // Hash and salt the new password.
        $hash_str = \Sodium\crypto_pwhash_scryptsalsa208sha256_str(
            $password,
            \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE,
            \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE
        );
        // Step 1: Check username is not taken.
        if !($this->findIDByUsername($username)) {
            // Step 2: Register the new username and corresponding ID in userlist.
            $res = $this->redis->hmset("userlist", [
                $username => $newUserID,
            ]);
            // Step 3: Create the new hash for the user.
            $res = $this->redis->hmset($key_user, [
                'username' => $username,
                'fullName' => $fullname,
                'email' => $email,
                'password' => $hash_str,
                'profile' => $profile,
            ]);
            // Step 4: Increment the usercount.
            $this->redis->incr("usercount");
        }
    }


    /**
     * Login user if password correct.
     *
     * @param string $username, string $password.
     *
     * @return bool
     */
    public function loginUser ($username, $password)
    {
        $currentUserID = $this->findIDByUsername($_POST['username']);
        if ($currentUserID) {
            $key_user = "userID:" . $this->redis->hget("userlist", $username); 
            $hash_str = $this->redis->hget($key_user, 'password');

            if (\Sodium\crypto_pwhash_scryptsalsa208sha256_str_verify($hash_str, $password)) {
                \Sodium\memzero($password);
                $this->addFeedback("LOGGED IN.");

                return true;

            } else {
                \Sodium\memzero($password);
                $this->addFeedback("FAILED LOG IN.");

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
     *
     * @return bool
     */
    public function isAdmin()
    {
        $username = $this->whoIsAuthenticated();
        $this->sessionTimeout();

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
                $this->AddFeedback('Du har varit inaktiv i 10 minuter och numera utloggad.');
            }
        }
    }
}
