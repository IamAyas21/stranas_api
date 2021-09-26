<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class VideoCarrousel 
{
   function get_video($id)
   {
      global $mysqli;
      $host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/webservice/uploads/videos/';
      //$host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/uploads/videos/';
      
      $query = "select carrouselvideos.id
                    , carrouselvideos.VideoName
                    , concat('".$host."',carrouselvideos.VideoUrl) as VideoUrl
                    , concat('".$host."',carrouselvideos.ThumbnailUrl) as ThumbnailUrl
                    , carrouselvideos.CreatedAt
                    , carrouselvideos.UpdatedAt
                from carrouselvideos";
      
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
                  'message' =>"Video Carrousel was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Video Carrousel was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      echo json_encode($response);    
   }

   function insert_update_video()
   {
        global $mysqli;
        $status = "0";

        $arrcheckpost = array('VideoName' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $random_name = "";
            $upload_name = "";

            $upload_dir = '../../uploads/videos/';

            $id = $_POST['id'];
            if($id == "")
            {
               
               $random_name = rand(1000,1000000)."-".strtolower($_FILES["VideoUpload"]["name"]);
               $upload_name = $upload_dir.strtolower($random_name);
               $upload_name = preg_replace('/\s+/', '-', $upload_name);

               $random_thumbnail_name = rand(1000,1000000)."-".strtolower($_FILES["ThumbnailUpload"]["name"]);
               $upload_thumbnail_name = $upload_dir.strtolower($random_thumbnail_name);
               $upload_thumbnail_name = preg_replace('/\s+/', '-', $upload_thumbnail_name);

               if(move_uploaded_file($_FILES["VideoUpload"]["tmp_name"] , $upload_name) && move_uploaded_file($_FILES["ThumbnailUpload"]["tmp_name"] , $upload_thumbnail_name))
               {
                   $urlFile = $random_name;
                   $urlFileThumbnail = $random_name;
                   $date = date('Y-m-d H:i:s');
                   $result = mysqli_query($mysqli, "INSERT INTO carrouselvideos SET
                   VideoName = '$_POST[FileName]',
                   VideoUrl = '$urlFile',
                   ThumbnailUrl = '$urlFileThumbnail',
                   CreatedAt = now()");
                    
                   if($result)
                   {
                      $response=array(
                         'status' => 1,
                         'message' =>'Carrousel Video Added Successfully.'
                      );
                   }
                   else
                   {
                      $response=array(
                         'status' => 0,
                         'message' =>'Carrousel Video Addition Failed.'
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
               $result = mysqli_query($mysqli, "UPDATE carrouselvideos SET
               VideoName = '$_POST[VideoName]',
               UpdatedAt = now()
               WHERE id='$id'");

               $status = "1";

               if($_FILES["VideoUpload"]["name"] != "")
               {
                  $query = "select carrouselvideos.VideoUrl, carrouselvideos.ThumbnailUrl from carrouselvideos where id = ".$id." LIMIT 1";
                  $result = mysqli_query($mysqli,$query);
                  $row = mysqli_fetch_assoc($result);
                  $fileName = $row['VideoUrl'];
                  $thumbnailName = $row['ThumbnailUrl'];
                  $upload_name = $upload_dir.$fileName;
                  $upload_thumbnail_name = $upload_dir.$thumbnailName;

                  if (file_exists($upload_name)) 
                  {
                     unlink($upload_name);
                  } 

                  $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["VideoUpload"]["name"]));
                  $upload_name = $upload_dir.strtolower($random_name);

                  $random_thumbnail_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["VideoUpload"]["name"]));
                  $upload_thumbnail_name = $upload_dir.strtolower($random_name);

                  if(move_uploaded_file($_FILES["VideoUpload"]["tmp_name"], $upload_name) && move_uploaded_file($_FILES["ThumbnailUpload"]["tmp_name"], $upload_thumbnail_name))
                  {
                     $urlFile = $random_name;
                     $urlFileThumbnail = $random_thumbnail_name;

                     $query = "UPDATE carrouselvideos SET VideoUrl = '$urlFile', ThumbnailUrl = '$urlFileThumbnail'  where id = '$id'";
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
                     'message' =>'Carrousel Video Updated Successfully.'
                  );
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'Carrousel Video Update Failed.'
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

   function delete_video($id)
   {
        global $mysqli;
        $upload_dir = '../../uploads/videos/';
        $query = "select carrouselvideos.VideoUrl, carrouselvideos.ThumbnailUrl from carrouselvideos where id = ".$id." LIMIT 1";
        $result = mysqli_query($mysqli,$query);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['VideoUrl'];
        $fileNameThumbnail = $row['ThumbnailUrl'];
        unlink($upload_dir.$fileName);
        unlink($upload_dir.$fileNameThumbnail);
        $query="DELETE FROM carrouselvideos WHERE id=".$id;
        if(mysqli_query($mysqli, $query))
        {
         $response=array(
            'status' => 1,
            'message' =>'Carrousel Video Deleted Successfully.'
         );
        }
        else
        {
         $response=array(
            'status' => 0,
            'message' =>'Carrousel Video Deletion Failed.'
         );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
   }
}
 ?>