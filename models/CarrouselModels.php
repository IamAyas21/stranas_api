<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');
class Carrousel 
{
   public function get_carrousel($id)
   {
      global $mysqli;
      $query = "SELECT * from carrousel";
      
      if($id != 0)
      {
         $query.=" WHERE id=".$id." LIMIT 1";
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
                  'message' =>"Carrousel was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 0,
                  'message' =>'Carrousel was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin:*');
      echo json_encode($response);    
   }

   public function insert_carrousel()
   {
      global $mysqli;
      $arrcheckpost = array('ImageUrl' => '', 'ImageDescription' => '');
      $hitung = count(array_intersect_key($_POST, $arrcheckpost));
      if($hitung == count($arrcheckpost)){
       
            $result = mysqli_query($mysqli, "INSERT INTO carrousel SET
            ImageUrl = '$_POST[ImageUrl]',
            ImageDescription = '$_POST[ImageDescription]'");
             
            if($result)
            {
               $response=array(
                  'status' => 1,
                  'message' =>'Carrousel Added Successfully.'
               );
            }
            else
            {
               $response=array(
                  'status' => 0,
                  'message' =>'Carrousel Addition Failed.'
               );
            }
      }else{
         $response=array(
                  'status' => 0,
                  'message' =>'Parameter Do Not Match'
               );
      }
      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin:*');
      echo json_encode($response);
   }

   function update_carrousel($id)
   {
      global $mysqli;
      $arrcheckpost = array('ImageUrl' => '', 'ImageDescription' => '');
      $hitung = count(array_intersect_key($_GET, $arrcheckpost));
      if($hitung == count($arrcheckpost)){
       
           $result = mysqli_query($mysqli, "UPDATE carrousel SET
           ImageUrl = '$_GET[ImageUrl]',
           ImageDescription = '$_GET[ImageDescription]'
           WHERE id='$id'");
       
         if($result)
         {
            $response=array(
               'status' => 1,
               'message' =>'Carrousel Updated Successfully.'
            );
         }
         else
         {
            $response=array(
               'status' => 0,
               'message' =>'Carrousel Updation Failed.'
            );
         }
      }else{
         $response=array(
                  'status' => 0,
                  'message' =>'Parameter Do Not Match'
               );
      }
      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin:*');
      echo json_encode($response);
   }

   function delete_carrousel($id)
   {
      global $mysqli;
      $query="DELETE FROM carrousel WHERE id=".$id;
      if(mysqli_query($mysqli, $query))
      {
         $response=array(
            'status' => 1,
            'message' =>'Carrousel Deleted Successfully.'
         );
      }
      else
      {
         $response=array(
            'status' => 0,
            'message' =>'Carrousel Deletion Failed.'
         );
      }
      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin:*');
      echo json_encode($response);
   }
}
 ?>