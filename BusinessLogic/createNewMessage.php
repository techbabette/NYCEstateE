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

if(!isset($_POST["message_type_id"])
|| !isset($_POST["title"])
|| !isset($_POST["body"]))
{
    echoUnprocessableEntity("All fields are required");
}

$messageType = $_POST["message_type_id"];
$title = $_POST["title"];
$body = $_POST["body"];

$reTitle = '/^[A-Z][a-z\']{0,19}(\s[A-Za-z][a-z\']{0,20}){1,4}$/';
$reBody = '/^[A-Z][a-z\']{0,19}(\s[A-Za-z][a-z\']{0,20}){2,14}$/';

if(!preg_match($reTitle, $title)){
    echoUnprocessableEntity("Message title does not match format (Between two and five words)");
}

if(!preg_match($reBody, $body)){
    echoUnprocessableEntity("Message body does not match format (Between three and fifteen words)");
}

$messageTypeExists = count(getEveryRowWhereParamFromTable("messagetypes", "message_type_id", $messageType)) > 0;
if(!$messageTypeExists){
    echoUnprocessableEntity("Invalid message type selected");
}

require("../DataAccess/messageFunctions.php");
try{
    createNewMessage($_SESSION["user"]["user_id"], $messageType, $title, $body);
}
catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully sent message";
http_response_code(201);
echo json_encode($result)
?>