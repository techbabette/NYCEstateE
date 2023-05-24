<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");

checkAccessLevel($requiredLevel);

require("../functions/navigationLocationFunctions.php");
$result;

try{
    $result["general"] = getAllNavigationLocations();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}