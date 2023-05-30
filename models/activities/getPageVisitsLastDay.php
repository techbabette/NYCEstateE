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
    $information = getPageVisits($timeLimit, $convertToPercentage, $sort, $page, $perPage);
    $page = $information["page"];
    $result["general"] = $information;
    if($page < 1){
        $result["general"]["lines"] = array();
        http_response_code(200);
        echo json_encode($result);
        die();
    }
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}


?>