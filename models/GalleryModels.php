<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/config/Connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/config/Connection.php');

class Gallery 
{
   public function get_gallery($id, $page)
   {
      global $mysqli;
      //$host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/webservice/uploads/gallery/';
      $host = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/uploads/gallery/';
      $query = "select gallery.id
                	, gallery.Category
                    , gallery.ImageName
                    , concat('".$host."',gallery.ImageUrl) as ImageUrl
                    , gallery.Descriptions
                from gallery
                order by gallery.Category asc";
      
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
                  'message' =>"Gallery was successfully loaded",
                  'data' => $data
               );
      }
      else{
            $response=array(
                  'status' => 1,
                  'message' =>'Gallery was unsuccessfully loaded',
                  'data' => $data
               );
      }
      
      header('Content-Type: application/json');
      echo json_encode($response);    
   }

   public function insert_update_gallery()
   {
        global $mysqli;
        $status = "0";

        $arrcheckpost = array('Category' => '', 'ImageName' => '', 'Descriptions' => '');
        $hitung = count(array_intersect_key($_POST, $arrcheckpost));
        
        if($hitung == count($arrcheckpost))
        {
            $random_name = "";
            $upload_name = "";

            $upload_dir = '../../uploads/gallery/';

            $id = $_POST['id'];
            if($id == "")
            {
                if (!empty(array_filter($_FILES['ImageUpload']['name']))) 
                {
                    foreach($_FILES['ImageUpload']['name'] as $id=>$val)
                    {
                        $random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["ImageUpload"]["name"][$id]));
                        $upload_name = $upload_dir.strtolower($random_name);
         
                        if(move_uploaded_file($_FILES["ImageUpload"]["tmp_name"][$id] , $upload_name))
                        {
                            $urlFile = $random_name;
                            $result = mysqli_query($mysqli, "INSERT INTO gallery SET
                            Category = '$_POST[Category]',
                            ImageName = '$_POST[ImageName]',
                            ImageUrl = '$urlFile',
                            Descriptions = '$_POST[Descriptions]',
                            CreatedAt = now()");
                             
                            if($result)
                            {
                               $response=array(
                                  'status' => 1,
                                  'message' =>'Gallery Added Successfully.'
                               );
                            }
                            else
                            {
                               $response=array(
                                  'status' => 0,
                                  'message' =>'Gallery Addition Failed.'
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
                }
            }
            else
            {
               $result = mysqli_query($mysqli, "UPDATE gallery SET
               Category = '$_POST[Category]',
               ImageName = '$_POST[ImageName]',
               Descriptions = '$_POST[Descriptions]'
               WHERE id='$id'");

               $status = "1";
			   if($_FILES["ImageUpload"]["name"] != "")
               {
				    $query = "select gallery.ImageUrl from gallery where id = ".$id." LIMIT 1";
					$result = mysqli_query($mysqli,$query);
					$row = mysqli_fetch_assoc($result);
					$fileName = $row['ImageUrl'];
					$upload_name = $upload_dir.$fileName;

					if (file_exists($upload_name)) 
					{
						unlink($upload_name);
					} 
					$random_name = preg_replace('/\s+/', '-',rand(1000,1000000)."-".strtolower($_FILES["ImageUpload"]["name"][$id] ));
					$upload_name = $upload_dir.strtolower($random_name);

					if(move_uploaded_file($_FILES["ImageUpload"]["tmp_name"] , $upload_name))
					{
						$urlFile = $random_name;
						$query = "UPDATE gallery SET UpdatedAt = now(), ImageUrl = '$urlFile' where id = '$id'";
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
                     'message' =>'Gallery Updated Successfully.'
                  );
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'Gallery Update Failed.'
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

   function delete_gallery($id)
   {
        global $mysqli;
        $upload_dir = '../../uploads/gallery/';
        $query = "select gallery.ImageUrl from gallery where id = ".$id." LIMIT 1";
        $result = mysqli_query($mysqli,$query);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['FileUrl'];
        unlink($upload_dir.$fileName);
        $query="DELETE FROM gallery WHERE id=".$id;
        if(mysqli_query($mysqli, $query))
        {
         $response=array(
            'status' => 1,
            'message' =>'Gallery Deleted Successfully.'
         );
        }
        else
        {
         $response=array(
            'status' => 0,
            'message' =>'Gallery Deletion Failed.'
         );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
   }
}
 ?>