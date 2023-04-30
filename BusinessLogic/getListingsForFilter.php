<?php
session_start();
require("../DataAccess/generalFunctions.php");

require("../DataAccess/listingFunctions.php");
$result;

$listingTitleFilter = "";
$listingBuildingTypeFilter = array();
$listingBoroughFilter = array();
$onlyFavorite = false;
$user_id = 0;

if(isset($_POST["titleFilter"])){
    $listingTitleFilter = $_POST["titleFilter"];
}

if(isset($_POST["buildingTypeFilter"])){
    $listingBuildingTypeFilter = $_POST["buildingTypeFilter"];
}

if(isset($_POST["boroughFilter"])){
    $listingBoroughFilter = $_POST["boroughFilter"];
}

if(isset($_POST["onlyFavorite"])){
    $onlyFavorite = true;
}

if($onlyFavorite && isset($_SESSION["user"])){
    $user_id = $_SESSION["user"]["user_id"];
}

try{
    $listings = getListingsForFilter($listingTitleFilter, $listingBuildingTypeFilter, $listingBoroughFilter, $user_id);
    $result["general"] = array();
    foreach($listings as $listing){
        $listingWithInformation["body"] = $listing;
        $listingWithInformation["img"] = getCurrentMainListingPhoto($listing["id"])["path"];
        $listingWithInformation["rooms"] = getRoomsOfListing($listing["id"]);
        array_push($result["general"], $listingWithInformation);
    }
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError($e);
}