<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Content-Type, Authorization');
header('Content-Type: application/json');

//require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/models/DocumentModels.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/DocumentModels.php');

$document = new DocumentCarrousel();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        $id = 0;
        $page = "";
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
         }
         
         $document->get_document($id, $page);
         break;
    case 'POST':
         $document->insert_update_document();
         break;
    case 'PUT':
         $id = 0;
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
         }

         break;
    case 'DELETE':
            $id=intval($_GET["id"]);
            $document->delete_document($id);
         break;
    case 'OPTIONS':
         break;
    default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}