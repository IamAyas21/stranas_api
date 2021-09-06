<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Content-Type, Authorization');
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/Stranas/models/LaporanAksiModels.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/webservice/models/LaporanAksiModels.php');

$laporan = new LaporanAksi();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        $id = 0;
        $page = "";
         if(!empty($_GET["id"]) && !empty($_GET["page"]))
         {
            $id=intval($_GET["id"]);
            $page=$_GET["page"];
         }
         
         $laporan->get_laporan($id, $page);
         break;
    case 'POST':
         $laporan->insert_update_laporan();
         break;
    case 'DELETE':
            $id=intval($_GET["id"]);
            $laporan->delete_laporan($id);
         break;
    case 'OPTIONS':
         break;
    default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
}