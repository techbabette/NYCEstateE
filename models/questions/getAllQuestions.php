<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

require("../DataAccess/surveyFunctions.php");
$result;

$sort = 2;
$deleted = false;
if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
}

try{
    $result["general"] = getAllQuestions($sort, $deleted);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}