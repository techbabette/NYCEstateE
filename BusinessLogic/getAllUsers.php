<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
// checkAccessLevel($requiredLevel);
require("../DataAccess/userFunctions.php");
$result;

$sort = 5;

// $json_params = file_get_contents("php://input");

// if (strlen($json_params) > 0 && isValidJSON($json_params)){
//     $decoded_params = json_decode($json_params, true);
//     $_POST = $decoded_params;
// }

// if(isset($_POST["sort"]) || isset($_GET["sort"])){
//     $sort = $_POST["sort"] ? $_POST["sort"] : $_GET["sort"];
// }

if(isset($_GET["sort"])){
    $sort = $_GET["sort"];
}

try{
    $result["general"] = getAllUsers($sort);
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}
?>