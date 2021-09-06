<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class FokusAksi 
{
    public function get_fokus($id, $page)
   {
      global $mysqli;
      $host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/webservice/uploads/fokus-aksi/';
      //$host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/uploads/laporan-aksi/';
      $query = "select fokusaksi.Id
                	, fokusaksi.CreatedAt
                    , fokusaksi.UpdatedAt
                    , fokusaksi.Title
                    , fokusaksi.SortDescription
                    , fokusaksi.Description
                    , concat('".$host."',fokusaksi.ThumbnailUrl) as ThumbnailUrl
                    , fokusaksi.View
                from fokusaksi";
      
      if($id != 0)
      {
         $query.=" WHERE Id=".$id;
      }
      
      $query .=" order by fokusaksi.Id";

      if($page == "user")
      {
            $queryViews = "select fokusaksi.View from fokusaksi WHERE Id=".$id." LIMIT 1";
            $resultViews = mysqli_query($mysqli,$queryViews);
            $row = mysqli_fetch_assoc($resultViews);
           
            $countViews = intval($row['View'])+1;
            mysqli_query($mysqli, "UPDATE fokusaksi SET
            View = $countViews
            WHERE id='$id'");
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
                  'message' =>"Fokus Aksi was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Fokus Aksi was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      echo json_encode($response);    
   }

    public function insert_update_fokus()
   {
        global $mysqli;
        $status = "0";

        $arrcheckpost = array('Title' => '', 'SortDescription' => '', 'Description' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $random_name = "";
            $upload_name = "";

            $upload_dir = '../../uploads/fokus-aksi/';

            $id = $_POST['id'];
            if($id == "")
            {
               
               $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["FileUpload"]["name"]));
               $upload_name = $upload_dir.strtolower($random_name);

               if(move_uploaded_file($_FILES["FileUpload"]["tmp_name"] , $upload_name))
               {
                   $urlFile = $random_name;
                   $result = mysqli_query($mysqli, "INSERT INTO fokusaksi SET
                   Title = '$_POST[Title]',
                   SortDescription = '$_POST[SortDescription]',
                   Description = '$_POST[Description]',
                   View = 0,
                   ThumbnailUrl = '$urlFile'");
                    
                   if($result)
                   {
                      $response=array(
                         'status' => 1,
                         'message' =>'Fokus Aksi Added Successfully.'
                      );
                   }
                   else
                   {
                      $response=array(
                         'status' => 0,
                         'message' =>'Fokus Aksi Addition Failed.'
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
               $result = mysqli_query($mysqli, "UPDATE fokusaksi SET
               Title = '$_POST[Title]',
               SortDescription = '$_POST[SortDescription]',
               Description = '$_POST[Description]'
               WHERE Id='$id'");

               $status = "1";

               if($_FILES["FileUpload"]["name"] != "")
               {
                  $query = "select fokusaksi.ThumbnailUrl from fokusaksi where Id = ".$id." LIMIT 1";
                  $result = mysqli_query($mysqli,$query);
                  $row = mysqli_fetch_assoc($result);
                  $fileName = $row['ThumbnailUrl'];
                  $upload_name = $upload_dir.$fileName;

                  if (file_exists($upload_name)) 
                  {
                     unlink($upload_name);
                  } 

                  $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["FileUpload"]["name"]));
                  $upload_name = $upload_dir.strtolower($random_name);

                  if(move_uploaded_file($_FILES["FileUpload"]["tmp_name"], $upload_name))
                  {
                     $urlFile = $random_name;
                     $query = "UPDATE fokusaksi SET ThumbnailUrl = '$urlFile' where Id = '$id'";
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
                     'message' =>'Fokus Aksi Updated Successfully.'
                  );
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'Fokus Aksi Update Failed.'
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
   function delete_fokus($id)
   {
        global $mysqli;
        $upload_dir = '../../uploads/fokus-aksi/';
        $query = "select fokusaksi.ThumbnailUrl from fokusaksi where Id = ".$id." LIMIT 1";
        $result = mysqli_query($mysqli,$query);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['FileUrl'];
        unlink($upload_dir.$fileName);
        $query="DELETE FROM fokusaksi WHERE Id=".$id;
        if(mysqli_query($mysqli, $query))
        {
         $response=array(
            'status' => 1,
            'message' =>'Fokus Aksi Deleted Successfully.'
         );
        }
        else
        {
         $response=array(
            'status' => 0,
            'message' =>'Fokus Aksi Deletion Failed.'
         );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
   }
}
 ?>