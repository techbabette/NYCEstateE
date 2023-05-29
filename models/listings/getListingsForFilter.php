<?php
session_start();
require("../functions/generalFunctions.php");

require("../functions/listingFunctions.php");

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}


$result;

$listingTitleFilter = "";
$listingBuildingTypeFilter = array();
$listingBoroughFilter = array();
$onlyFavorite = false;
$user_id = 0;
$sortType = 0;

$page = 1;
$perPage = 2;

if(isset($_GET["page"])){
    $page = $_GET["page"];
}

if(isset($_POST["page"])){
    $page = $_POST["page"];
}

if(isset($_GET["perPage"])){
    $page = $_GET["perPage"];
}

if(isset($_POST["perPage"])){
    $page = $_POST["perPage"];
}

if(isset($_POST["titleFilter"])){
    $listingTitleFilter = $_POST["titleFilter"];
}

if(isset($_POST["buildingTypeFilter"])){
    $listingBuildingTypeFilter = $_POST["buildingTypeFilter"];
}

if(isset($_POST["boroughFilter"])){
    $listingBoroughFilter = $_POST["boroughFilter"];
}

if(isset($_POST["onlyFavorite"]) && isset($_SESSION["user"])){
    $onlyFavorite = true;
}

if(isset($_POST["sortType"])){
    $sortType = $_POST["sortType"];
}


if(isset($_SESSION["user"])){
    $user_id = $_SESSION["user"]["user_id"];
}


try{
    $result["general"]["count"] = getNumOfListingsForFilter($listingTitleFilter, $listingBuildingTypeFilter, $listingBoroughFilter, $user_id, $onlyFavorite, $sortType);
    $result["general"]["maxPage"] = ceil($result["general"]["count"] / $perPage);
    if($page > $result["general"]["maxPage"]) $page = $result["general"]["maxPage"];
    $listings = getListingsForFilter($listingTitleFilter, $listingBuildingTypeFilter, $listingBoroughFilter, $user_id, $onlyFavorite, $sortType, $page, $perPage);
    $result["general"]["listings"] = array();
    $result["general"]["page"] = $page;
    $result["general"]["perPage"] = $perPage;
    foreach($listings as $listing){
        $listingWithInformation["body"] = $listing;
        $listingWithInformation["img"] = getCurrentMainListingPhoto($listing["id"])["path"];
        $listingWithInformation["rooms"] = getRoomsOfListing($listing["id"]);
        array_push($result["general"]["listings"], $listingWithInformation);
    }
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}