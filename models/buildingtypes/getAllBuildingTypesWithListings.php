<?php
session_start();
require("../functions/generalFunctions.php");

require("../functions/buildingTypeFunctions.php");
$result;

try{
    $result["general"] = getAllBuildingTypesWithListings();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}