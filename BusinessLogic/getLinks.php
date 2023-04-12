<?php
include("../DataAccess/linkFunctions.php");
include("../DataAccess/userFunctions.php");
require("../DataAccess/generalFunctions.php");

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

//Initialize access level as one
$accessLevel = 1;
$loggedIn = false;
$result;
$currentPage = $_POST["currentPage"];
$allowed = false;
session_start();
//If user is logged in, get their access level and set logged in to true
if(isset($_SESSION["user"])){
    $accessLevel = getUserLevel($_SESSION["user"]["user_id"])["level"];
    if($accessLevel != 1){
        $loggedIn = true;        
    }
}
try{
    $result["general"]["links"] = getLinks($accessLevel, $loggedIn);
    $result["general"]["accessLevel"] = $accessLevel;
    //For every link found, check if it matches current page
    foreach($result["general"]["links"] as $link){
        if($link["href"] == $currentPage){
            $allowed = true;
            break;
        }
    }
    //If page is in list of pages available to user
    if($allowed){
        http_response_code(200);
        echo json_encode($result);
    }
    //If page is not available to user, redirect
    else{
        http_response_code(308);
    }
}
catch (PDOException $e){
    $result["error"] = $e;
    http_response_code(500);
    echo json_encode($result);
}

?>