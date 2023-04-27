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

if(!isset($_POST["messageTypeName"]))
{
    echoUnprocessableEntity("All fields are required");
}

$messageTypeName = $_POST["messageTypeName"];

$reMessageTypeName = '/^[A-Z][a-z]{1,19}$/';

if(!preg_match($reMessageTypeName, $messageTypeName)){
    echoUnprocessableEntity("Message type name has to be one capitalized word");
}

try{
    insertSingleParamater("messagetypes", $messageTypeName, "message_type_name");
}

catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully created new message type";
http_response_code(201);
echo json_encode($result)
?>