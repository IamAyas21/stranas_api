<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class LaporanAksi 
{
   public function get_laporan($id, $page)
   {
      global $mysqli;
      //$host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/webservice/uploads/laporan-aksi/';
      $host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/uploads/laporan-aksi/';
      $query = "select laporanaksi.id
                	, laporanaksi.Category
                    , laporanaksi.Title
                    , laporanaksi.FileName
                    , concat('".$host."',laporanaksi.FileUrl) as FileUrl
                    , laporanaksi.Summary
                    , laporanaksi.Views
                from laporanaksi";
      
      if($id != 0)
      {
         $query.=" WHERE id=".$id." LIMIT 1";
      }

      if($page == "user")
      {
            $queryViews = "select laporanaksi.views from laporanaksi WHERE id=".$id." LIMIT 1";
            $resultViews = mysqli_query($mysqli,$queryViews);
            $row = mysqli_fetch_assoc($resultViews);
           
            $countViews = intval($row['views'])+1;
            mysqli_query($mysqli, "UPDATE laporanaksi SET
            Views = $countViews
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
                  'message' =>"Laporan Aksi was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Laporan Aksi was unsuccessfully loaded',
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

        $arrcheckpost = array('Category' => '', 'Title' => '', 'FileName' => '', 'Summary' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $random_name = "";
            $upload_name = "";

            $upload_dir = '../../uploads/laporan-aksi/';

            $id = $_POST['id'];
            if($id == "")
            {
               
               $random_name = rand(1000,1000000)."-".strtolower($_FILES["FileUpload"]["name"]);
               $upload_name = $upload_dir.strtolower($random_name);
               $upload_name = preg_replace('/\s+/', '-', $upload_name);

               if(move_uploaded_file($_FILES["FileUpload"]["tmp_name"] , $upload_name))
               {
                   $urlFile = $random_name;
                   $result = mysqli_query($mysqli, "INSERT INTO laporanaksi SET
                   Category = '$_POST[Category]',
                   Title = '$_POST[Title]',
                   FileName = '$_POST[FileName]',
                   FileUrl = '$urlFile',
                   Summary = '$_POST[Summary]'");
                    
                   if($result)
                   {
                      $response=array(
                         'status' => 1,
                         'message' =>'Laporan Aksi Added Successfully.'
                      );
                   }
                   else
                   {
                      $response=array(
                         'status' => 0,
                         'message' =>'Laporan Aksi Addition Failed.'
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
               $result = mysqli_query($mysqli, "UPDATE laporanaksi SET
               Category = '$_POST[Category]',
               Title = '$_POST[Title]',
               FileName = '$_POST[FileName]',
               Summary = '$_POST[Summary]'
               WHERE id='$id'");

               $status = "1";

               if($_FILES["FileUpload"]["name"] != "")
               {
                  $query = "select laporanaksi.FileUrl from laporanaksi where id = ".$id." LIMIT 1";
                  $result = mysqli_query($mysqli,$query);
                  $row = mysqli_fetch_assoc($result);
                  $fileName = $row['FileUrl'];
                  $upload_name = $upload_dir.$fileName;

                  if (file_exists($upload_name)) 
                  {
                     unlink($upload_name);
                  } 

                  $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["FileUpload"]["name"]));
                  $upload_name = $upload_dir.strtolower($random_name);
                  //$upload_name = preg_replace('/\s+/', '-', $upload_name);

                  if(move_uploaded_file($_FILES["FileUpload"]["tmp_name"], $upload_name))
                  {
                     $urlFile = $random_name;
                     $query = "UPDATE laporanaksi SET FileUrl = '$urlFile' where id = '$id'";
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

   function delete_laporan($id)
   {
        global $mysqli;
        $upload_dir = '../../uploads/laporan-aksi/';
        $query = "select laporanaksi.FileUrl from laporanaksi where id = ".$id." LIMIT 1";
        $result = mysqli_query($mysqli,$query);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['FileUrl'];
        unlink($upload_dir.$fileName);
        $query="DELETE FROM laporanaksi WHERE id=".$id;
        if(mysqli_query($mysqli, $query))
        {
         $response=array(
            'status' => 1,
            'message' =>'Laporan Aksi Deleted Successfully.'
         );
        }
        else
        {
         $response=array(
            'status' => 0,
            'message' =>'Laporan Aksi Deletion Failed.'
         );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
   }
}
 ?>