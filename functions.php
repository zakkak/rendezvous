<?php
    session_start();
    //render the csrf token input field
    function csrfToken()
    {
        $token = $_SESSION['token'] = bin2hex(random_bytes(32));
        $field = "<input type='hidden' name='token' value=$token>";
        echo $field;
    }
    //renew the csrf token
    function renewToken()
    {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    //check if the csrf token is valid
    function validateToken()
    {
        if(!hash_equals($_SESSION['token'],$_POST['token']))
        {
            die("Invalid Token!");
        }
    }
?>