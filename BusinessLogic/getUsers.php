<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);
require("../DataAccess/userFunctions.php");
$result;

try{
    $result["general"] = getAllUsers();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError()
}
?>