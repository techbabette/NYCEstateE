<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");

checkAccessLevel($requiredLevel);

require("../DataAccess/buildingTypeFunctions.php");
$result;

try{
    $result["general"] = getAllBuildingTypes();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    $result["error"] = "An unexpected error occured";
    http_response_code(500);
    echo json_encode($result);
}