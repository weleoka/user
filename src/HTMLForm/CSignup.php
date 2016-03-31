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
                "id" => __CLASS__,
            ],
            [
                "pwd" => [
                    "type" =>"text",
                    "label" => "Password",
                ],

                "pwdAgain" => [
                    "type" => "text",
                    "label" => "Password again",
                    "validation" => [
                        "match" => "pwd"
                    ],
                ],

                "submit" => [
                    "type" => "submit",
                    "value" => "Create user",
                    "callback" => [$this, "callbackSubmit"]
                ],
            ]
        );
    }



    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmit()
    {
        $matches = $this->value("pwd") === $this->value("pwdAgain")
            ? "YES"
            : "NO";
        echo "CALLBACK CALLBACK CALLBACK.";
        $this->addOutput("<p>#callbackSubmit()</p>");
        $this->addOutput("<p>Passwords matches: $matches</p>");
        $this->saveInSession = true;

        return true;
    }
}
