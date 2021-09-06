<?php
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Methods:*');
    header('Access-Control-Allow-Credentials:true');
    header('Access-Control-Allow-Headers:Content-Type, Authorization');
    header('Content-Type: application/json');

    require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/models/UsersModels.php');
    //require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/UsersModels.php');

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
        echo json_encode($response);  
    }
?>