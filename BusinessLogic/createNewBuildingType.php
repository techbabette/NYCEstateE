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

if(!isset($_POST["buildingTypeName"]))
{
    echoUnprocessableEntity("All fields are required");
}

$buildingTypeName = $_POST["buildingTypeName"];

$rebuildingTypeName = '/^[A-Z][a-z\']{1,50}(\s[A-Za-z][a-z\']{1,50}){0,3}$/';

if(!preg_match($rebuildingTypeName, $buildingTypeName)){
    echoUnprocessableEntity("Building type name does not match format");
}

try{
    insertSingleParamater("buildingtypes", $buildingTypeName, "type_name");
}

catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Success";
http_response_code(201);
echo json_encode($result)
?>