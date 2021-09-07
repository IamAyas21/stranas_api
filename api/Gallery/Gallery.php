<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Content-Type, Authorization');
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/models/GalleryModels.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/GalleryModels.php');

$gallery = new Gallery();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        $id = 0;
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
         }
         
         $gallery->get_gallery($id);
         break;
    case 'POST':
         $gallery->insert_update_gallery();
         break;
    case 'DELETE':
            $id=intval($_GET["id"]);
            $gallery->delete_gallery($id);
         break;
    case 'OPTIONS':
         break;
    default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}