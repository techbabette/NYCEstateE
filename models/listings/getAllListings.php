<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

require("../DataAccess/listingFunctions.php");
$result;

$sort = 0;
$deleted = false;
if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
}

try{
    $result["general"] = getAllListings($sort, $deleted);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}