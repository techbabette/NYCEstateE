<?php
session_start();
$requiredLevel = 2;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if(!isset($_POST["answerId"])){
    echoImproperRequest("All fields are required");
}

$answerId = $_POST["answerId"];

require("../DataAccess/surveyFunctions.php");

$userAllowedToAnswer = checkIfUserAllowedToAnswer($_SESSION["user"]["user_id"], $answerId);

if(!$userAllowedToAnswer){
    echoUnprocessableEntity("You already answered this question");
}

try{
    saveUserAnswer($_SESSION["user"]["user_id"], $answerId);
    $result["general"] = "Successfully answered question";
    http_response_code(201);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError($e);
}