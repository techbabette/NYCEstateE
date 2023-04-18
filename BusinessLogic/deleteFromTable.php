<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

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
    case "Links" : 
        $dataTable = "links";
        $dataParam = "link_id";
        break;
}

if($dataTable == ""){
    echoUnprocessableEntity("No such table");
}

try{
    deleteSingleRow($dataTable, $dataParam, $requestedId);
    $result["general"]["msg"] = "Successfully deleted";
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError()
}

?>