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

require("../DataAccess/linkFunctions.php");
$result;

try{
    $result["general"] = getAllLinks();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    http_response_code(500);
    $result["error"] = $e;
    echo json_encode($result);
}