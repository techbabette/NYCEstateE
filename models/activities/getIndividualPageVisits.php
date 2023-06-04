<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");
checkAccessLevel($requiredLevel);

require("../functions/activityFunctions.php");
$result;


$sort = 1;
$page = 1;
$perPage = 5;

if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
}

if(isset($_GET["page"])){
    $page = $_GET["page"];
}

try{
    $result["general"]["count"] = getNumberOfPageVisits();
    $result["general"]["maxPage"] = ceil($result["general"]["count"] / $perPage);
    if($page > $result["general"]["maxPage"]) $page = $result["general"]["maxPage"];
    $result["general"]["page"] = $page;
    $result["general"]["perPage"] = $perPage;
    $result["general"]["lines"] = array();

    if($page < 1){
        http_response_code(200);
        echo json_encode($result);
        die();
    }

    $visitsInfo = getIndividualPageVisits($page, $perPage, $sort);
    $result["general"]["lines"] = $visitsInfo["lines"];

    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}


?>