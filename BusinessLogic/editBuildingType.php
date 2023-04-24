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
$buildingTypeId = $_POST["id"];

$reBuildingTypeName = '/^[A-Z][a-z\']{1,50}(\s[A-Za-z][a-z\']{1,50}){0,3}$/';

if(!preg_match($reBuildingTypeName, $buildingTypeName)){
    echoUnprocessableEntity("Building type does not match format");
}

$buildingTypeExists = count(getEveryRowWhereParamFromTable("buildingtypes", "building_type_id", $buildingTypeId)) > 0;

if(!$buildingTypeExists){
    echoUnprocessableEntity("Building type with provided id does not exist");
}

try{
    updateTextValue("buildingtypes", "type_name", $buildingTypeName, "building_type_id", $buildingTypeId);
}

catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully edited building type";
http_response_code(200);
echo json_encode($result)
?>