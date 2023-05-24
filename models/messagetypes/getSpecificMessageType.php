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
    echoImproperRequest("Must provide a valid message type id");
}

$id = $_POST["id"];

require("../functions/messageFunctions.php");

try{
    $messageType = getSpecificMessageType($id);
    if(!$messageType){
        echoNotFound();
    }
    $result["general"] = $messageType;
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}