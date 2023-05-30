<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");
checkAccessLevel($requiredLevel);

require("../functions/activityFunctions.php");
$result;

$timeLimit = true;
$convertToPercentage = false;
$sort = 0;

$page = 1;
$perPage = 5;

if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
}

if(isset($_GET["page"])){
    $page = $_GET["page"];
}

try{
    $result["general"] = getPageVisits($timeLimit, $convertToPercentage, $sort, $page, $perPage);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}


?>