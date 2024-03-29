<?php
session_start();
require("../functions/generalFunctions.php");

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}


$result;

if(!isset($_POST["listing_id"])){
    echoImproperRequest("All fields are required");
}

$listing_id = $_POST["listing_id"];

$user_id = 0;


if(isset($_SESSION["user"])){
    $user_id = $_SESSION["user"]["user_id"];
}

require("../functions/listingFunctions.php");
require("../functions/informationFunctions.php");

try{
    $listing = getDetailedListing($listing_id, $user_id);
    if(!$listing){
        echoNotFound("Listing not found"); 
    }
    if(!$listing["active"]){
        echoGone("Listing no longer active");
    }
    $result["general"]["body"] = $listing;
    $result["general"]["img"] = getCurrentMainListingPhoto($listing_id)["path"];
    $result["general"]["rooms"] = getRoomsOfListing($listing_id);
    $result["general"]["number"] = getListingPhoneNumber();
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}
?>