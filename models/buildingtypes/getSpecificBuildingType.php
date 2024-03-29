<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if(!isset($_POST["id"])){
    echoImproperRequest("Must provide a valid building type id");
}

$id = $_POST["id"];

require("../functions/buildingTypeFunctions.php");

try{
    $buildingType = getSpecificBuildingType($id);
    if(!$buildingType){
        echoNotFound();
    }
    $result["general"] = $buildingType;
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}