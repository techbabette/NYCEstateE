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

if(!isset($_POST["messageTypeName"]) || !isset($_POST["id"]))
{
    echoImproperRequest("All fields are required");
}

$messageTypeName = $_POST["messageTypeName"];
$messageTypeId = $_POST["id"];

$reMessageTypeName = '/^[A-Z][a-z]{1,19}$/';

if(!preg_match($reMessageTypeName, $messageTypeName)){
    echoUnprocessableEntity("Message type name has to be one capitalized word");
}

$messageTypeExists = count(getEveryRowWhereParamFromTable("messagetypes", "message_type_id", $messageTypeId)) > 0;

if(!$messageTypeExists){
    echoUnprocessableEntity("Message type with provided id does not exist");
}

try{
    updateTextValue("messagetypes", "message_type_name", $messageTypeName, "message_type_id", $messageTypeId);
}

catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully edited message type";
http_response_code(200);
echo json_encode($result)
?>