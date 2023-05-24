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

if(!isset($_POST["boroughName"]))
{
    echoImproperRequest("All fields are required");
}

$boroughName = $_POST["boroughName"];

$reBoroughName = '/^[A-Z][a-z\']{1,50}(\s[A-Za-z][a-z\']{1,50}){0,3}$/';

if(!preg_match($reBoroughName, $boroughName)){
    echoUnprocessableEntity("Borough name does not match format");
}

try{
    insertSingleParamater("boroughs", $boroughName, "borough_name");
}
catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully created new borough";
http_response_code(201);
echo json_encode($result)
?>