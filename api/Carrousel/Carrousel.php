<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Content-Type, Authorization');
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/models/CarrouselModels.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/CarrouselModels.php');

$carrousel = new Carrousel();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
         }
         
         $carrousel->get_carrousel($id);
         break;
   case 'POST':
         $carrousel->insert_carrousel();
         break;
    case 'PUT':
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
            $carrousel->update_carrousel($id);
         }   
         break; 
   case 'DELETE':
            $id=intval($_GET["id"]);
            $carrousel->delete_carrousel($id);
         break;
   case 'OPTIONS':
         break;
   default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}