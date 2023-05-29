<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");
checkAccessLevel($requiredLevel);

require("../functions/activityFunctions.php");
$result;


$sort = 0;
$page = 1;
$perPage = 5;

if(isset($_POST["page"])){
    $page = $_POST["page"];
}

try{
    $result["general"] = getPageVisits($timeLimit, $convertToPercentage, $sort);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}


?>