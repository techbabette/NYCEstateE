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

if(!isset($_POST["boroughName"]) || !isset($_POST["id"]))
{
    echoImproperRequest("All fields are required");
}

$boroughName = $_POST["boroughName"];
$boroughId = $_POST["id"];

$reBoroughName = '/^[A-Z][a-z\']{1,50}(\s[A-Za-z][a-z\']{1,50}){0,3}$/';

if(!preg_match($reBoroughName, $boroughName)){
    echoUnprocessableEntity("Borough name does not match format");
}

$boroughExists = count(getEveryRowWhereParamFromTable("boroughs", "borough_id", $boroughId)) > 0;

if(!$boroughExists){
    echoUnprocessableEntity("Borough with provided id does not exist");
}

try{
    updateTextValue("boroughs", "borough_name", $boroughName, "borough_id", $boroughId);
}

catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully edited borough";
http_response_code(200);
echo json_encode($result)
?>