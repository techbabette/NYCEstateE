<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if(!isset($_POST["id"])){
    $result["error"] = "Must provide a valid link id";
    http_response_code(422);
    echo json_encode($result);
    die();
}

$id = $_POST["id"];

require("../DataAccess/linkFunctions.php");

try{
    $result["general"] = getSpecificLink($id);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}