<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");
checkAccessLevel($requiredLevel);

require("../functions/activityFunctions.php");
$result;

$sort = 0;

if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
}

try{
    $result["general"] = getLogins();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}


?>