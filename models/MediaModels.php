<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class Media 
{
   public function get_media($id)
   {
       global $mysqli;
      $host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/webservice/uploads/media/';
      //$host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/uploads/media/';
      $query = "select media.id
                	, media.Category
                    , media.Title
                    , media.Body
                    , media.VideoName
                    , concat('".$host."',media.VideoUrl) as VideoUrl
                from media";
      
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
                  'message' =>"Media was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Media was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      echo json_encode($response);   
   }

   public function insert_update_laporan()
   {
        global $mysqli;
        $status = "0";

        $arrcheckpost = array('Category' => '', 'Title' => '', 'VideoName' => '', 'Body' => '', 'VideoUrl' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $random_name = "";
            $upload_name = "";

            $upload_dir = '../../uploads/media/';

            $id = $_POST['id'];
            if($id == "")
            {
               
               $random_name = rand(1000,1000000)."-".strtolower($_FILES["VideoUpload"]["name"]);
               $upload_name = $upload_dir.strtolower($random_name);
               $upload_name = preg_replace('/\s+/', '-', $upload_name);

               if(move_uploaded_file($_FILES["VideoUpload"]["tmp_name"] , $upload_name))
               {
                   $urlFile = $random_name;
                   $result = mysqli_query($mysqli, "INSERT INTO media SET
                   Category = '$_POST[Category]',
                   Title = '$_POST[Title]',
                   VideoName = '$_POST[VideoName]',
                   VideoUrl = '$urlFile',
                   Body = '$_POST[Body]'");
                    
                   if($result)
                   {
                      $response=array(
                         'status' => 1,
                         'message' =>'Media Added Successfully.'
                      );
                   }
                   else
                   {
                      $response=array(
                         'status' => 0,
                         'message' =>'Media Addition Failed.'
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
               $result = mysqli_query($mysqli, "UPDATE media SET
               Category = '$_POST[Category]',
               Title = '$_POST[Title]',
               VideoName = '$_POST[VideoName]',
               Body = '$_POST[Body]'
               WHERE id='$id'");

               $status = "1";

               if($_FILES["VideoUpload"]["name"] != "")
               {
                  $query = "select media.VideoUrl from media where id = ".$id." LIMIT 1";
                  $result = mysqli_query($mysqli,$query);
                  $row = mysqli_fetch_assoc($result);
                  $fileName = $row['VideoUrl'];
                  $upload_name = $upload_dir.$fileName;

                  if (file_exists($upload_name)) 
                  {
                     unlink($upload_name);
                  } 

                  $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["VideoUpload"]["name"]));
                  $upload_name = $upload_dir.strtolower($random_name);
                  //$upload_name = preg_replace('/\s+/', '-', $upload_name);

                  if(move_uploaded_file($_FILES["VideoUpload"]["tmp_name"], $upload_name))
                  {
                     $urlFile = $random_name;
                     $query = "UPDATE media SET VideoUrl = '$urlFile' where id = '$id'";
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
                     'message' =>'Laporan Aksi Updated Successfully.'
                  );
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'Laporan Aksi Update Failed.'
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

   function update_media($id)
   {
      global $mysqli;
      $arrcheckpost = array('Title' => '', 'Body' => '', 'VideoName' => '', 'VideoUrl' => '','Category' => '');
      $hitung = count(array_intersect_key($_GET, $arrcheckpost));
      if($hitung == count($arrcheckpost))
      { 
           $result = mysqli_query($mysqli, "UPDATE media SET
           Title = '$_GET[Title]',
           Body = '$_GET[Body]',
           VideoName = '$_GET[VideoName]',
           VideoUrl = '$_GET[VideoUrl]',
           Category = '$_GET[Category]'
           WHERE id='$id'");
       
         if($result)
         {
            $response=array(
               'status' => 1,
               'message' =>'Media Updated Successfully.'
            );
         }
         else
         {
            $response=array(
               'status' => 0,
               'message' =>'Media Updation Failed.'
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

   function delete_media($id)
   {
        global $mysqli;
        $upload_dir = '../../uploads/media/';
        $query = "select media.VideoUrl from media where id = ".$id." LIMIT 1";
        $result = mysqli_query($mysqli,$query);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['VideoUrl'];
        unlink($upload_dir.$fileName);
        $query="DELETE FROM media WHERE id=".$id;
        if(mysqli_query($mysqli, $query))
        {
        $response=array(
            'status' => 1,
            'message' =>'Media Deleted Successfully.'
        );
        }
        else
        {
        $response=array(
            'status' => 0,
            'message' =>'Media Deletion Failed.'
        );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
   }
}
 ?>