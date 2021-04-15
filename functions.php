<?php
    session_start();
    //render the csrf token input field
    function csrfToken()
    {
        $token = $_SESSION['token'] = bin2hex(random_bytes(20));
        $field = "<input type='hidden' name='token' value=$token>";
        echo $field;
    }
    //check if the csrf token is valid
    function validateToken()
    {
        if(!hash_equals($_SESSION['token'],$_POST['token']))
        {
            die("Invalid Token!");
        }
    }
    //check if a parameter is indeed numeric
    function checkNumericParam($param)
    {
        if(!is_numeric($param))
        {
            die("Invalid Request!");
        }
    }
?>