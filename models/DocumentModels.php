<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class DocumentCarrousel 
{
   function get_document($id)
   {
      global $mysqli;
      $host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/webservice/uploads/documents/';
      //$host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/uploads/documents/';
      
      $query = "select carrouseldocuments.id
                    , carrouseldocuments.DocumentName
                    , carrouseldocuments.Title
                    , carrouseldocuments.Description
                    , concat('".$host."',carrouseldocuments.DocumentUrl) as DocumentUrl
                    , concat('".$host."',carrouseldocuments.ThumbnailUrl) as ThumbnailUrl
                    , carrouseldocuments.CreatedAt
                    , carrouseldocuments.UpdatedAt
                from carrouseldocuments";
      
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
                  'message' =>"Document Carrousel was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Document Carrousel was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      echo json_encode($response);    
   }

   function insert_update_document()
   {
        global $mysqli;
        $status = "0";

        $arrcheckpost = array('DocumentName' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $random_name = "";
            $upload_name = "";

            $upload_dir = '../../uploads/documents/';

            $id = $_POST['id'];
            if($id == "")
            {
               $random_name = rand(1000,1000000)."-".strtolower($_FILES["DocumentUpload"]["name"]);
               $upload_name = $upload_dir.strtolower($random_name);
               $upload_name = preg_replace('/\s+/', '-', $upload_name);

               $random_thumbnail_name = rand(1000,1000000)."-".strtolower($_FILES["ThumbnailUpload"]["name"]);
               $upload_thumbnail_name = $upload_dir.strtolower($random_thumbnail_name);
               $upload_thumbnail_name = preg_replace('/\s+/', '-', $upload_thumbnail_name);

               if(move_uploaded_file($_FILES["DocumentUpload"]["tmp_name"] , $upload_name) && move_uploaded_file($_FILES["ThumbnailUpload"]["tmp_name"] , $upload_thumbnail_name))
               {
                   $urlFile = $random_name;
                   $urlFileThumbnail = $random_thumbnail_name;

                   $date = date('Y-m-d H:i:s');
                   $result = mysqli_query($mysqli, "INSERT INTO carrouseldocuments SET
                   Title = '$_POST[Title]',
                   Description = '$_POST[Description]',
                   DocumentName = '$_POST[DocumentName]',
                   DocumentUrl = '$urlFile',
                   ThumbnailUrl = '$urlFileThumbnail',
                   CreatedAt = now()");
                    
                   if($result)
                   {
                      $response=array(
                         'status' => 1,
                         'message' =>'Carrousel Document Added Successfully.'
                      );
                   }
                   else
                   {
                      $response=array(
                         'status' => 0,
                         'message' =>'Carrousel Document Addition Failed.'
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
               $result = mysqli_query($mysqli, "UPDATE carrouseldocuments SET
               Title = '$_POST[Title]',
               Description = '$_POST[Description]',
               DocumentName = '$_POST[DocumentName]',
               UpdatedAt = now()
               WHERE id='$id'");

               $status = "1";

               if($_FILES["DocumentUpload"]["name"] != "")
               {
                  $query = "select carrouseldocuments.DocumentUrl, carrouseldocuments.ThumbnailUrl from carrouseldocuments where id = ".$id." LIMIT 1";
                  $result = mysqli_query($mysqli,$query);
                  $row = mysqli_fetch_assoc($result);
                  $fileName = $row['DocumentUrl'];  
                  $fileThumbnailName = $row['ThumbnailUrl'];
                  $upload_name = $upload_dir.$fileName;

                  if (file_exists($upload_name)) 
                  {
                     unlink($upload_name);
                  } 

                  $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["DocumentUpload"]["name"]));
                  $upload_name = $upload_dir.strtolower($random_name);

                  $random_thumbnail_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["ThumbnailUpload"]["name"]));
                  $upload_thumbnail_name = $upload_dir.strtolower($random_thumbnail_name);

                  if(move_uploaded_file($_FILES["DocumentUpload"]["tmp_name"], $upload_name) && move_uploaded_file($_FILES["ThumbnailUpload"]["tmp_name"], $upload_thumbnail_name))
                  {
                     $urlFile = $random_name;
                     $urlFileThumbnail = $random_thumbnail_name;
                     $query = "UPDATE carrouseldocuments SET DocumentUrl = '$urlFile', ThumbnailUrl = '$urlFileThumbnail' where id = '$id'";
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
                     'message' =>'Carrousel Document Updated Successfully.'
                  );
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'Carrousel Document Update Failed.'
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

   function delete_document($id)
   {
        global $mysqli;
        $upload_dir = '../../uploads/documents/';
        $query = "select carrouseldocuments.DocumentUrl, carrouseldocuments.ThumbnailUrl from carrouseldocuments where id = ".$id." LIMIT 1";
        $result = mysqli_query($mysqli,$query);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['DocumentUrl'];
        $fileName = $row['ThumbnailUrl'];
        unlink($upload_dir.$fileName);
        $query="DELETE FROM carrouseldocuments WHERE id=".$id;
        if(mysqli_query($mysqli, $query))
        {
         $response=array(
            'status' => 1,
            'message' =>'Carrousel Document Deleted Successfully.'
         );
        }
        else
        {
         $response=array(
            'status' => 0,
            'message' =>'Carrousel Document Deletion Failed.'
         );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
   }
}
 ?>