<?php
include("../DataAccess/linkFunctions.php");


//Get data from submitted JSON
$data = json_decode(file_get_contents('php://input'), true);
$_POST = $data;

$accessLevel = 1;
$loggedIn = false;
$result;
$currentPage = $_POST["currentPage"];
$allowed = false;
session_start();
//If user is logged in, get their access level and set logged in to true
if(isset($_SESSION["user"])){
    $accessLevel = $_SESSION["user"]["level"];
    $loggedIn = true;
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