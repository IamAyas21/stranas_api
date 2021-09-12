<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Content-Type, Authorization');
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/models/MediaModels.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/MediaModels.php');

$media = new Media();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
         $id = 0;
         $page = "";
         $filter = "";

         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
         }
         if(!empty($_GET["page"]))
         {
            $page=$_GET["page"];
         }
          if(!empty($_GET["filter"]))
         {
            $filter=$_GET["filter"];
         }

         $media->get_media($id,$page,$filter);
         break;
    case 'POST':
         $media->insert_media();
         break;
    case 'PUT':
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
            $media->update_media($id);
         }   
         break; 
    case 'DELETE':
            $id=intval($_GET["id"]);
            $media->delete_media($id);
         break;
    case 'OPTIONS':
         break;
    default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}