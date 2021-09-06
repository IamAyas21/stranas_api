<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class Inquiry 
{
    public function get_inquiry($id)
   {
      global $mysqli;
      $query = "select kontak.Id
                	, kontak.CreatedAt
                    , kontak.Name
                    , kontak.Email
                    , kontak.Subject
                from kontak";
      
      if($id != 0)
      {
         $query.=" WHERE Id=".$id." LIMIT 1";
      }

      $data=array();
      $result=$mysqli->query($query);
      while($row=mysqli_fetch_object($result))
      {
         $data[]=$row;
      }

      if(count($data) > 0)
      {
            $response=array(
                  'status' => 1,
                  'message' =>"Inquiry was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Inquiry was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      echo json_encode($response);    
   }

    public function insert_inquiry()
   {
        global $mysqli;

        $arrcheckpost = array('Name' => '', 'Email' => '', 'Subject' => '', 'BrowserName' => '', 'BrowserVersion' => '', 'UserAgent' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $result = mysqli_query($mysqli, "INSERT INTO kontak SET
            Name = '$_POST[Name]',
            Email = '$_POST[Email]',
            Subject = '$_POST[Subject]',
            BrowserName = '$_POST[BrowserName]',
            BrowserVersion = '$_POST[BrowserVersion]',
            UserAgent = '$_POST[UserAgent]'");
            
            if($result)
            {
              $response=array(
                 'status' => 1,
                 'message' =>'Inquiry Added Successfully.'
              );
            }
            else
            {
              $response=array(
                 'status' => 0,
                 'message' =>'Inquiry Addition Failed.'
              );
            }
      }
      else
      {
         $response=array(
                  'status' => 0,
                  'message' =>'Parameter Do Not Match'
               );
      }
      header('Content-Type: application/json');
      echo json_encode($response);
   }
}
 ?>