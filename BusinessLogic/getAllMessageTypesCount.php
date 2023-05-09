<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");

checkAccessLevel($requiredLevel);

require("../DataAccess/messageFunctions.php");
$result;

try{
    $result["general"] = getAllMessageTypesCount();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}