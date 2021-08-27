<?php
require_once('../../models/CarrouselModels.php');
$carrousel = new Carrousel();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
            $carrousel->get_carrousel($id);
         }
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
   default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}