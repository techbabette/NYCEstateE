<?php
session_start();
$requiredLevel = 2;
require("../functions/generalFunctions.php");

checkAccessLevel($requiredLevel);

require("../functions/messageFunctions.php");
$result;

try{
    $result["general"] = getAllMessageTypes();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}