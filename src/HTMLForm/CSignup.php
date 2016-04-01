<?php

namespace Weleoka\HTMLForm;

/**
 * Example of CFormModel implementation.
 *
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
                'id' => __CLASS__,
            ],
            [
                'username' => [
                    'type'        => 'text',
                    'label'       => 'Username',
                    //'id'          => '',
                    //'class'       => '',
                    'placeholder' => 'Username',
                    'maxlength'   => 255,
                    'validation'  => ['not_empty'],
                ],
                'fullname' => [
                    'type'        => 'text',
                    'label'       => 'Full name',
                    //'id'          => '',
                    //'class'       => '',                    
                    'required'    => true,
                    'maxlength'   => 255,
                    'placeholder' => 'Full name',
                    'validation'  => ['not_empty'],
                ],
                'email' => [
                    'type'        => 'email',
                    'label'       => 'Email',
                    //'id'          => '',
                    //'class'       => '',
                    'required'    => true,
                    'placeholder' => 'email address',
                    'validation'  => ['not_empty', 'email_adress'],
                ],  
                'profile' => [
                    'type'        => 'textarea',
                    'label'       => 'Profile',
                    //'id'          => '',
                    //'class'       => '',
                    'required'    => true,
                    'placeholder' => 'User profile',
                    'validation'  => ['not_empty'],
                ],
                'password' => [
                    'type' =>'password',
                    'label' => 'Password',
                    //'id'          => '',
                    //'class'       => '',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'passwordAgain' => [
                    'type' => 'password',
                    'label' => 'Password again',
                    //'id'          => '',
                    //'class'       => '',
                    'required'    => true,
                    'validation'  => ['match' => 'password', 'not_empty'],
                ],

                'submit' => [
                    'type' => 'submit',
                    'value' => 'Create user',
                    //'id'          => '',
                    //'class'       => '',
                    'callback' => [$this, 'callbackSubmit']
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
        $this->addOutput('<p>#callbackSubmit()</p>');

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
}


/*        $user = $this->forum->getUser();
        // This logs the contribution and resets session TTL.
        $this->forum->userContributionLog($user);
        $this->forum->resetTTL();

        $this->answers->save([
            'userID'            => $user->id,
            'parentID'      => $question->id,
            'parentTitle'     => $question->title,
            'name'            => $user->name,
            'content'        => $this->textFilter->doFilter($form->Value('content') , 'markdown'),
            'email'            => $user->email,
            'timestamp'     => getTime(),
        ]);
        // For each answer added increase the answerCount of question.
        $parameters['answerCount'] = $question->answerCount + 1;
        $this->questions->update($parameters);

        return true;*/


/*
                "pwdAgain" => [
                    "type" => "text",
                    "label" => "Password again",
                    "validation" => [
                        "match" => "pwd"
                    ],
                ],
*/

/*
            $form = $this->form;
            $form = $form->create([], [
                'content' => [
                    'type'        => 'textarea',
                    'label'       => '',
                    'placeholder' => 'Skriv ett svar',
                    'validation'  => ['not_empty'],
                ],
                'submit' => [
                    'type'      => 'submit',
                    'class'        => 'bigButton',
                    'callback'  => function($form) use ($question){
                    }
                ],
            ]);
*/



/*

<form method='POST' action='signup.php'>
    <h3>Sign up form</h3>
    <div>
        Username
        <input type='text' placeholder='' value='' required='' name='username' />
    </div>
    <div>
        Full name
        <input type='text' placeholder='' value='' required='' name='fullname' />
    </div>
    <div>
        Profile
        <input type='text' placeholder='' value='' required='' name='profile' />
    </div>
    <div>
        Password
        <input type='text' placeholder='' value='' required='' name='password' />
    </div>
    <div>
        <input type='submit' value='Sign up' name='signup' /><br>
        <input type='reset' value='Reset'><br>
    </div>
</form>

*/