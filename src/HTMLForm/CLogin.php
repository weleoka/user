<?php

namespace Weleoka\HTMLForm;

/**
 * Example of CFormModel implementation.
 *
 */
class CLogin extends \Mos\HTMLForm\CFormModel
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct(
            [],
            [
                "username" => [
                    "type" =>"text",
                    "label" => "Username",
                ],
                "password" => [
                    "type" =>"password",
                    "label" => "Password",
                ],
                "submit" => [
                    "type" => "submit",
                    "value" => "Log in",
                    "callback" => [$this, "callbackSubmit"]
                ],
            ]
        );
    }


    /**
     * Callback for submit-button.
     *
     * @return bool
     */
    public function callbackSubmit()
    {
        $user = new \Weleoka\Users\UserRedis();
        $result = null;
        
        if ($user->login([
            'username'  => $this->value('username'),
            'password'  => $this->value('password'),
        ])) {

            return true;
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
    }
}