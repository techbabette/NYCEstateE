<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
// If user not logged in, die
if(!isset($_SESSION["user"])){
    echoNoPermission();
}
//If user's access level is too low, die
if($_SESSION["user"]["level"] < $requiredLevel){
    echoNoPermission();
}

$data = json_decode(file_get_contents('php://input'), true);
$_POST = $data;

$requestedTable = $_POST["table"];
$requestedId = $_POST["id"];

$dataTable = "";
$dataParam = "";

switch($requestedTable){
    case "Users" : 
        $dataTable = "users";
        $dataParam = "user_id";
        break;
    case "Boroughs" :
        $dataTable = "boroughs";
        $dataParam = "borough_id";
        break;
    case "Listings" : 
        $dataTable = "listings";
        $dataParam = "listing_id";
        break;
}

if($dataTable == ""){
    http_response_code(422);
    $result["error"] = "No such table";
    echo json_encode($result);
}

try{
    deleteSingleRow($dataTable, $dataParam, $requestedId);
    $result["general"]["msg"] = "Successfully deleted";
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    http_response_code(200);
    $result["error"]["msg"] = $e;
    echo json_encode($result);
}

?>