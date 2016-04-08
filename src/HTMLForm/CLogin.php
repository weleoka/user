<?php

namespace Weleoka\HTMLForm;

/**
 * Example of CFormModel implementation.
 *
 */
class CLogin extends \Mos\HTMLForm\CFormModel
{
    private $formKey;
    private $old_formKey;


    /**
     * Constructor
     *
     */
    public function __construct($user_instance)
    {
        $this->user = $user_instance;
        $this->formKey = $this->generateKey();

        //Store the form key in the session
        $_SESSION['formKey'] = $this->formKey;
        //$this->old_formKey = $_SESSION['formKey'];

        //We need the previous key so we store it
        if(isset($_SESSION['formKey']))
        {
            $this->old_formKey = $_SESSION['formKey'];
        }

        parent::__construct(
            [],
            [
                "formKey" => [
                    "type"          => "hidden",
                    "value"         => $this->formKey,
                    //'validation'    => ['custom_test' => [
                       //'message'       => 'Form key validation failed.',
                        //'test'          => $this->validateFormKey($this->formKey),
                    //]],
                ],
                "username" => [
                    "type"          => "text",
                    "label"         => "Username",
                ],
                "password" => [
                    "type"          => "password",
                    "label"         => "Password",
                ],
                "submit" => [
                    "type"          => "submit",
                    "value"         => "Log in",
                    "callback"      => [$this, "callbackSubmit"]
                ],
            ]
        );
    }

    
    /**
     * Validate a recvdKey against the stored $old_formKey.
     *
     * @param string $recvdKey the key delivered by form.
     * @return bool true/false if key validates.
     */
    public function validateFormKey($recvdKey)
    {
        echo "VALIDATING form key: " . $recvdKey . " against: " . $this->old_formKey;
        // Svar_dump($this->old_formKey);

        //We use the old formKey and not the new generated version
        if(isset($recvdKey) && $recvdKey != $this->old_formKey) {
            echo "TRUE KEY TRUE KEY";
            return true;

        } else {
            echo "FALSE KEY FALSE KEY";
            return false;
        }
    }


    /**
     * Callback for submit-button.
     *
     * @return bool
     */
    public function callbackSubmit()
    {   

        // $user = new \Weleoka\Users\UserRedis();
        if ($this->validateFormKey($this->formKey)) {
        
            if ($this->user->login([
                'username'  => $this->value('username'),
                'password'  => $this->value('password'),
            ])) {
        
                return true;
            } 
        }
        
        return false; 
    }


    /**
     * Callback What to do if the form was submitted successfully?
     *
     */
    public function callbackSuccess()
    {
        $this->addOutput("<p>LOGGED IN!</p>");
        $this->addOutput("<p>You have logged in successfully.</p>");
        header("Location: " . $_SERVER['PHP_SELF']);
        die();
    }


    /**
     * Callback What to do when submitted form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->addOutput("<p>LOGIN FAILED.</p>");
        $this->addOutput("<p>Invalid username or password.</p>");
        header("Location: " . $_SERVER['PHP_SELF']);
        die();
    }


    /**
     * Generate a random hash to use as key.
     *
     * @return string md5 hash of ip and uniqid().
     */
    private function generateKey()
    {
        //Get the IP-address of the user
        $ip = $_SERVER['REMOTE_ADDR'];
         
        //We use mt_rand() instead of rand() because it is better for generating random numbers.
        //We use 'true' to get a longer string.
        //See http://www.php.net/mt_rand for a precise description of the function and more examples.
        $uniqid = uniqid(mt_rand(), true);
         
        //Return the hash
        return md5($ip . $uniqid);
    }
}