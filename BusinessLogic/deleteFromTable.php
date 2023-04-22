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

$type = "hard";

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
        $type = "soft";
        break;
    case "Links" : 
        $dataTable = "links";
        $dataParam = "link_id";
        break;
}

if($dataTable == ""){
    echoUnprocessableEntity("No such table");
}

$exists = count(getEveryRowWhereParamFromTable($dataTable, $dataParam, $requestedId)) > 0;

if(!$exists){
    echoUnprocessableEntity(substr($requestedTable, 0, -1)." with given id does not exist");
}

try{
    if($type == "hard"){
        deleteSingleRow($dataTable, $dataParam, $requestedId);
    }
    else{
        softDeleteSingleRow($dataTable, $dataParam, $requestedId);
    }
    $result["general"] = "Successfully deleted ".substr($requestedTable, 0, -1);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}

?>