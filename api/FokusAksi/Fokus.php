
text/x-generic Fokus.php ( PHP script, ASCII text, with CRLF line terminators )
<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Content-Type, Authorization');
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/FokusAksiModels.php');

$fokus = new FokusAksi();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
     case 'GET':
        $id = 0;
        $page = "";
         if(!empty($_GET["id"]))
         {
            $id=intval($_GET["id"]);
         }
         
         if(!empty($_GET["page"]))
         {
            $page=$_GET["page"];
         }
         
         $fokus->get_fokus($id, $page);
         break;
     case 'POST':
         $fokus->insert_update_fokus();
         break;
    case 'DELETE':
            $id=intval($_GET["id"]);
            $fokus->delete_fokus($id);
         break;
    case 'OPTIONS':
         break;
    default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}