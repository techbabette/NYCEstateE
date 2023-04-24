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

if(!isset($_POST["roomTypeName"]))
{
    echoUnprocessableEntity("All fields are required");
}

$roomTypeName = $_POST["roomTypeName"];

$reRoomTypeName = '/^[A-Z][a-z\']{1,50}(\s[A-Za-z][a-z\']{1,50}){0,3}$/';
// $reDescription = '/^[A-Z][a-z\']{0,50}(\s[A-Za-z][a-z\']{0,50})*$/';

if(!preg_match($reRoomTypeName, $roomTypeName)){
    echoUnprocessableEntity("Room type name does not match format");
}

try{
    insertSingleParamater("roomtypes", $roomTypeName, "room_name");
}

catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully created new room type";
http_response_code(201);
echo json_encode($result)
?>