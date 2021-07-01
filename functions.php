<?php
    session_start();
    //render the csrf token input field
    function csrfToken()
    {
        if(function_exists("random_bytes"))
        {
            try
            {
                $token = $_SESSION['token'] = bin2hex(random_bytes(20));
            }
            catch(Exception $e)
            {
                die("Exception thrown: " . $e);
            }
        }
        else
        {
            $token = $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(20));
        }
        $field = "<input type='hidden' name='token' value=$token>";
        echo $field;
    }
    //check if the csrf token is valid
    function validateToken()
    {
        if(!hash_equals($_SESSION['token'],$_POST['token']))
        {
            echo("The CSRF token has expired! Please try again.");
            die("<br><a href='?op=" . filter_var($_GET['op'],FILTER_SANITIZE_SPECIAL_CHARS) . "'>Back</a>");
        }
    }
    //check if a parameter is indeed numeric
    function checkNumericParam($param)
    {
        if(!is_numeric($param))
        {
            echo("Parameters must be numeric! Please try again");
            die("<br><a href='?op=" . filter_var($_GET['op'],FILTER_SANITIZE_SPECIAL_CHARS) . "'>Back</a>");
        }
    }
    //check if the string contains html tags
    function hasHTMLTags($string)
    {
        if($string === strip_tags($string))
            return false;
        return true;
    }
    //check if the query doesn't contain html tags
    function checkValidQuery($query)
    {
        if(hasHTMLTags($query))
        {
            die("HTML tags are not allowed!");
        }
    }
?>