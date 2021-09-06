<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Content-Type, Authorization');
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/InquiryModels.php');

$inquiry = new Inquiry();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
     case 'GET':
        $id = 0;
        $page = "";
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
         }
         
         $inquiry->get_inquiry($id);
         break;
     case 'POST':
         $inquiry->insert_inquiry();
         break;
    case 'DELETE':
           
         break;
    case 'OPTIONS':
         break;
    default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}