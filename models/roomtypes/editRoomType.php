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

if(!isset($_POST["roomTypeName"]))
{
    echoImproperRequest("All fields are required");
}

$roomTypeName = $_POST["roomTypeName"];
$roomTypeId = $_POST["id"];

$reRoomTypeName = '/^[A-Z][a-z\']{1,50}(\s[A-Za-z][a-z\']{1,50}){0,3}$/';

if(!preg_match($reRoomTypeName, $roomTypeName)){
    echoUnprocessableEntity("Room type name does not match format");
}

$roomTypeExists = count(getEveryRowWhereParamFromTable("roomtypes", "room_type_id", $roomTypeId)) > 0;

if(!$roomTypeExists){
    echoUnprocessableEntity("Room type with provided id does not exist");
}

try{
    updateTextValue("roomtypes", "room_name", $roomTypeName, "room_type_id", $roomTypeId);
}

catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully edited room type";
http_response_code(200);
echo json_encode($result)
?>