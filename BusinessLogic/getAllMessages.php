<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");

checkAccessLevel($requiredLevel);

require("../DataAccess/messageFunctions.php");
$result;

$sort = -1;

if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
}

try{
    $result["general"] = getAllMessages($sort);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}