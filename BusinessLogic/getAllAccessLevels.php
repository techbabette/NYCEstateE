<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
// If user not logged in, die
if(!isset($_SESSION["user"])){
    echoNoPermission();
}
//If user's access level is too low, die
if($_SESSION["user"]["level"] < $requiredLevel){
    echoNoPermission();
}

require("../DataAccess/accessLevelFunctions.php");
$result;

try{
    $result["general"] = getAllAccessLevels();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    $result["error"] = "An unexpected error occured";
    http_response_code(500);
    echo json_encode($result);
}