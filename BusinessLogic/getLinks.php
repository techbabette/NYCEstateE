<?php
include("../DataAccess/linkFunctions.php");
$accessLevel = 1;
$loggedIn = false;
$result;
if(isset($_SESSION["user"])){
    $accessLevel = $_SESSION["user"]["level"];
    $loggedIn = true;
}
try{
    $result["general"] = getLinks($accessLevel, $loggedIn);
    $result["accessLevel"] = $accessLevel;
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    $result["error"] = $e;
    http_response_code(500);
    echo json_encode($result);
}

?>