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

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if(!isset($_POST["id"])){
    $result["error"] = "Must provide a valid user id";
    http_response_code(422);
    echo json_encode($result);
    die();
}

$id = $_POST["id"];

require("../DataAccess/userFunctions.php");

try{
    $result["general"] = getSpecificUser($id);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    $result["error"] = $e;
    http_response_code(500);
    echo json_encode($result);
}