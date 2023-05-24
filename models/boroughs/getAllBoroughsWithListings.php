<?php
session_start();
require("../DataAccess/generalFunctions.php");

require("../DataAccess/boroughFunctions.php");
$result;

try{
    $result["general"] = getAllBoroughsWithListings();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}