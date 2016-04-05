<?php

namespace Weleoka\HTMLForm;

/**
 * Example of CFormModel implementation.
 * 
 * CForm will call the callbackSubmit, fail and success methods.
 */
class CSignup extends \Mos\HTMLForm\CFormModel
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct(
            [
                // 'id' => __CLASS__,
            ],
            [
                'username'      => [
                    'type'          => 'text',
                    'label'         => 'Username',
                    //'id'          => '',
                    //'class'       => '',
                    'required'      => true,
                    'placeholder'   => 'Username',
                    'maxlength'     => 255,
                    'validation'    => ['not_empty'],
                ],
                'fullname'      => [
                    'type'          => 'text',
                    'label'         => 'Full name',
                    //'id'          => '',
                    //'class'       => '',                    
                    'required'      => true,
                    'maxlength'     => 255,
                    'placeholder'   => 'Full name',
                    'validation'    => ['not_empty'],
                ],
                'email'         => [
                    'type'          => 'email',
                    'label'         => 'Email',
                    //'id'          => '',
                    //'class'       => '',
                    'required'      => true,
                    'placeholder'   => 'email address',
                    'validation'    => ['not_empty', 'email_adress'],
                ],  
                'profile'       => [
                    'type'          => 'textarea',
                    'label'         => 'Profile',
                    //'id'          => '',
                    //'class'       => '',
                    'required'      => true,
                    'placeholder'   => 'User profile',
                    'validation'    => ['not_empty'],
                ],
                'password'      => [
                    'type'          =>'password',
                    'label'         => 'Password',
                    //'id'          => '',
                    //'class'       => '',
                    'required'      => true,
                    'validation'    => ['not_empty'],
                ],
                'passwordAgain' => [
                    'type'          => 'password',
                    'label'         => 'Password again',
                    //'id'          => '',
                    //'class'       => '',
                    'required'      => true,
                    'validation'    => ['match' => 'password', 'not_empty'],
                ],
                'agreement'      => [
                    'type'          => 'checkbox',
                    'label'         => 'I agree',
                    //'id'          => '',
                    //'class'       => '',
                    'required'      => true,
                    'validation'    => ['must_accept'],
                ],

                'submit'        => [
                    'type'          => 'submit',
                    'value'         => 'Create user',
                    //'id'          => '',
                    //'class'       => '',
                    'callback'      => [$this, 'callbackSubmit']
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
        $user->signup([
            'username'  => $this->value('username'),
            'fullname'  => $this->value('fullname'),
            'profile'   => $this->value('profile'),
            'email'     => $this->value('email'),
            'password'  => $this->value('password'),
        ]);

        return true;
    }


    /**
     * Callback What to do if the form was submitted successfully?
     *
     */
    public function callbackSuccess()
    {
        $this->addOutput("<p>REGISTERED!</p>");
        $this->addOutput("<p>Form came through. We welcome you aboard ravensGrib service..</p>");
        header("Location: " . $_SERVER['PHP_SELF']);
    }


    /**
     * Callback What to do when submitted form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->addOutput("<p>SIGN UP FAILED.</p>");
        $this->addOutput("<p>Form was submitted but something went wrong.</p>");
        header("Location: " . $_SERVER['PHP_SELF']);
    }
}