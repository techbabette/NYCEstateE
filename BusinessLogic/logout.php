<?php
session_start();
$result;
if(isset($_SESSION["user"])){
    session_unset();
    http_response_code(302);
    $result["general"] = "Successfully logged out";
    echo json_encode($result);
    die();
}
else{
    http_response_code(404);
    $result["error"] = "Error 404: Page not found";
    echo json_encode($result);
}
?>