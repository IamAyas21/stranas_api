<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');
class Users 
{
 
   public function login($username, $password)
   {
      global $mysqli;
      $query = "SELECT * from users where username='".$username."' and password='".$password."'";
      
      $result=$mysqli->query($query);
      $data=mysqli_fetch_object($result);

      if(count($data) > 0)
      {
            $response=array(
                  'status' => 1,
                  'message' =>"login was successfully",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 0,
                  'message' =>'username or password do not match'
               );
      }
      
      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin:*');
      echo json_encode($response);    
   }
}
 ?>