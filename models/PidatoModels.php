<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class PidatoCarrousel 
{
   function get_pidato($id)
   {
      global $mysqli;
      $host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/webservice/uploads/pidatos/';
      //$host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/uploads/pidatos/';
      
      $query = "select carrouselpidato.id
                    , carrouselpidato.PidatoName
                    , concat('".$host."',carrouselpidato.PidatoUrl) as PidatoUrl
                    , carrouselpidato.CreatedAt
                    , carrouselpidato.UpdatedAt
                from carrouselpidato";
      
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
                  'message' =>"Pidato Carrousel was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Pidato Carrousel was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      echo json_encode($response);    
   }

   function insert_update_pidato()
   {
        global $mysqli;
        $status = "0";

        $arrcheckpost = array('PidatoName' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $random_name = "";
            $upload_name = "";

            $upload_dir = '../../uploads/pidatos/';

            $id = $_POST['id'];
            if($id == "")
            {
               
               $random_name = rand(1000,1000000)."-".strtolower($_FILES["PidatoUpload"]["name"]);
               $upload_name = $upload_dir.strtolower($random_name);
               $upload_name = preg_replace('/\s+/', '-', $upload_name);

               if(move_uploaded_file($_FILES["PidatoUpload"]["tmp_name"] , $upload_name))
               {
                   $urlFile = $random_name;
                   $date = date('Y-m-d H:i:s');
                   $result = mysqli_query($mysqli, "INSERT INTO carrouselpidato SET
                   PidatoName = '$_POST[FileName]',
                   PidatoUrl = '$urlFile',
                   CreatedAt = now()");
                    
                   if($result)
                   {
                      $response=array(
                         'status' => 1,
                         'message' =>'Carrousel Pidato Added Successfully.'
                      );
                   }
                   else
                   {
                      $response=array(
                         'status' => 0,
                         'message' =>'Carrousel Pidato Addition Failed.'
                      );
                   }
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'File upload failed.'
                  );
               }
            }
            else
            {
               $result = mysqli_query($mysqli, "UPDATE carrouselpidato SET
               PidatoName = '$_POST[PidatoName]',
               UpdatedAt = now()
               WHERE id='$id'");

               $status = "1";

               if($_FILES["PidatoUpload"]["name"] != "")
               {
                  $query = "select laporanaksi.FileUrl from carrouselpidato where id = ".$id." LIMIT 1";
                  $result = mysqli_query($mysqli,$query);
                  $row = mysqli_fetch_assoc($result);
                  $fileName = $row['PidatoUrl'];
                  $upload_name = $upload_dir.$fileName;

                  if (file_exists($upload_name)) 
                  {
                     unlink($upload_name);
                  } 

                  $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["PidatoUpload"]["name"]));
                  $upload_name = $upload_dir.strtolower($random_name);
                  //$upload_name = preg_replace('/\s+/', '-', $upload_name);

                  if(move_uploaded_file($_FILES["PidatoUpload"]["tmp_name"], $upload_name))
                  {
                     $urlFile = $random_name;
                     $query = "UPDATE carrouselpidato SET PidatoUrl = '$urlFile' where id = '$id'";
                     $result = mysqli_query($mysqli, $query);

                     $status = "1";
                  }
                  else
                  {
                     $status = "0";

                     $response=array(
                        'status' => 0,
                        'message' =>'File upload failed.'
                     );
                  } 
               }

               if($status == "1")
               {
                  $response=array(
                     'status' => 1,
                     'message' =>'Carrousel Pidato Updated Successfully.'
                  );
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'Carrousel Pidato Update Failed.'
                  );
               }
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

   function delete_pidato($id)
   {
        global $mysqli;
        $upload_dir = '../../uploads/pidatos/';
        $query = "select carrouselpidato.PidatoUrl from carrouselpidato where id = ".$id." LIMIT 1";
        $result = mysqli_query($mysqli,$query);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['PidatoUrl'];
        unlink($upload_dir.$fileName);
        $query="DELETE FROM carrouselpidato WHERE id=".$id;
        if(mysqli_query($mysqli, $query))
        {
         $response=array(
            'status' => 1,
            'message' =>'Carrousel Pidato Deleted Successfully.'
         );
        }
        else
        {
         $response=array(
            'status' => 0,
            'message' =>'Carrousel Pidato Deletion Failed.'
         );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
   }
}
 ?>