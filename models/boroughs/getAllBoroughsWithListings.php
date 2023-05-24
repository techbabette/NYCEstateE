<?php
session_start();
require("../functions/generalFunctions.php");

require("../functions/boroughFunctions.php");
$result;

try{
    $result["general"] = getAllBoroughsWithListings();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}