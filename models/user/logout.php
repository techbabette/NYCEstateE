<?php
session_start();
$result;
require("../DataAccess/generalFunctions.php");
if(isset($_SESSION["user"])){
    session_unset();
    http_response_code(200);
    $result["general"] = "Successfully logged out";
    echo json_encode($result);
    die();
}
else{
    echoUnauthorized();
}
?>