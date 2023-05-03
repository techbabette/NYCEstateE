<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$result;

if(!isset($_FILES["listingPhoto"])){
    echoUnprocessableUnit("All fields are required");
}

if
(!isset($_POST["listingTitle"]) 
|| !isset($_POST["listingDescription"])
|| !isset($_POST["listingAddress"])
|| !isset($_POST["listingSize"])
|| !isset($_POST["listingPrice"])
|| !isset($_POST["listingBorough"])
|| !isset($_POST["listingBuildingType"])
)
{
    echoUnprocessableEntity("All fields are required");
}

$listingTitle = $_POST["listingTitle"];
$listingDescription = $_POST["listingDescription"];
$listingAddress = $_POST["listingAddress"];
$listingSize = $_POST["listingSize"];
$listingPrice = $_POST["listingPrice"];
$listingBorough = $_POST["listingBorough"];
$listingBuildingType = $_POST["listingBuildingType"];

$reTitle = '/^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/';
$reAddress = '/^(([A-Z][a-z\d\']+)|([0-9][1-9]*\.?))(\s[A-Za-z\d][a-z\d\']+){0,7}\s(([1-9][0-9]{0,5}[\/-]?[A-Z])|([1-9][0-9]{0,5})|(NN))\.?$/';
$reDescription = '/^[A-Z][a-z\']{0,50}(\s[A-Za-z][a-z\']{0,50})*$/';

if(!preg_match($reTitle, $listingTitle)){
    echoUnprocessableEntity("Title does not match format");
}
if(!preg_match($reDescription, $listingDescription)){
    echoUnprocessableEntity("Description does not match format");
}
if(!preg_match($reAddress, $listingAddress)){
    echoUnprocessableEntity("Address does not match format", $listingAddress);
}

if($listingSize < 30){
    echoUnprocessableEntity("Size cannot be below 30 feet");
}

if($listingSize > 100000){
    echoUnprocessableEntity("Size cannot be above 100000 feet");
}

if($listingPrice < 1000){
    echoUnprocessableEntity("Price cannot be below 1000$");
}

if($listingPrice > 1000000000){
    echoUnprocessableEntity("Price cannot be above 1000000000$");
}

$target_dir = "../resources/imgs/";
$nameToSave = basename($_FILES["listingPhoto"]["name"]);
$imageFileType = strtolower(pathinfo($nameToSave,PATHINFO_EXTENSION));
$newFileName = time().uniqid(rand());
$target_file = $target_dir.$newFileName.".".$imageFileType;

$check = getimagesize($_FILES["listingPhoto"]["tmp_name"]);

if($check === false) {
    echoUnprocessableEntity("Uploaded file is not an image");
}

if ($_FILES["listingPhoto"]["size"] > 8000000) {
    echoUnprocessableEntity("Uploaded file is too large");
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
    echoUnprocessableEntity("Uploaded file of incorrect type".$imageFileType);
}

$boroughExists = count(getEveryRowWhereParamFromTable("boroughs", "borough_id", $listingBorough)) > 0;
if(!$boroughExists){
    echoUnprocessableEntity("Invalid borough selected");
}
$buildingTypeExists = count(getEveryRowWhereParamFromTable("buildingtypes", "building_type_id", $listingBuildingType)) > 0;

if(!$buildingTypeExists){
    echoUnprocessableEntity("Invalid building type selected");
}
$rooms = array();

if(isset($_POST["listingRooms"])){
    $rooms = json_decode($_POST["listingRooms"]);    
}

foreach($rooms as $room){
    $roomExists = count(getEveryRowWhereParamFromTable("roomtypes", "room_type_id", $room->roomId)) > 0;
    if(!$roomExists){
        echoUnprocessableEntity("Invalid room type selected");
    }
    if($room->count < 1){
        echoUnprocessableEntity("Number of any room cannot be below one");
    }
    if($room->count > 99){
        echoUnprocessableEntity("Number of any room cannot be above 99");
    }
}

require("../DataAccess/listingFunctions.php");
require("../DataAccess/imageFunctions.php");
try{
    $lastInsertedId = createNewListing($listingBorough, $listingBuildingType, $listingTitle, $listingDescription, $listingAddress, $listingSize);
    saveListingPrice($lastInsertedId, $listingPrice);
    saveMainListingPhoto($lastInsertedId, $newFileName.".".$imageFileType);
    foreach($rooms as $room){
        saveListingRoom($lastInsertedId, $room->roomId, $room->count);
    }
}
catch (PDOException $e){
    echoUnexpectedError();
}

// move_uploaded_file($_FILES["listingPhoto"]["tmp_name"], $target_file);
saveAdjustedPhotoToDisk($_FILES["listingPhoto"], $target_file, 640, 360);
$result["general"] = "Successfully created new listing";
http_response_code(201);
echo json_encode($result)
?>