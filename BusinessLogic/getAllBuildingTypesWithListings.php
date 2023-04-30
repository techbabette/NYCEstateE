<?php
session_start();
require("../DataAccess/generalFunctions.php");

require("../DataAccess/buildingTypeFunctions.php");
$result;

try{
    $result["general"] = getAllBuildingTypesWithListings();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}