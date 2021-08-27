<?php
    require_once('../../models/usersmodels.php');
    $users = new Users();

    if(!empty($_GET["username"]) && !empty($_GET["password"]))
    {
        $username=$_GET["username"];
        $password=$_GET["password"];
        $users->login($username,$password);
    }
    else{
        $response=array(
            'status' => 0,
            'message' =>'username and password cant be empty.'
         );
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin:*');
        echo json_encode($response);  
    }
?>