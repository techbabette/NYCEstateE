<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$result;

if
(!isset($_POST["listingTitle"]) 
|| !isset($_POST["listingDescription"])
|| !isset($_POST["listingId"])
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
$listingId = $_POST["listingId"];

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
$imgUpload = false;
if(isset($_FILES["listingPhoto"])){
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
    $imgUpload = true;
}

$listingExists = count(getEveryRowWhereParamFromTable("listings", "listing_id", $listingId)) > 0;
if(!$listingExists){
    echoUnprocessableEntity("Invalid listing selected");
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

// $roomsToDelete = array();
// if(isset($_POST["listingRoomsDeleted"])){
//     $roomsToDelete = json_decode($_POST["listingRooms"]);
// }

foreach($rooms as $room){
    //Optimize this by calling once to get every room_type_id, then checking the current id against the list
    $roomExists = count(getEveryRowWhereParamFromTable("roomtypes", "room_type_id", $room->roomId)) > 0;
    if(!$roomExists){
        echoUnprocessableEntity("Invalid room type selected");
    }
}

// foreach($roomsToDelete as $room){
//     //Optimize this by calling once to get every room_type_id, then checking the current id against the list
//     $roomExists = count(getEveryRowWhereParamFromTable("roomtypes", "room_type_id", $room->roomId)) > 0;
//     if(!$roomExists){
//         echoUnprocessableEntity("Invalid room type selected");
//     }
// }

require("../DataAccess/listingFunctions.php");

try{
    $lastInsertedId = editListing($listingId, $listingBorough, $listingBuildingType, $listingTitle, $listingDescription, $listingAddress, $listingSize);
    $currentPrice = getPriceOfListing($listingId)["price"];
    //If current price different from submitted price
    if($currentPrice != $listingPrice){
        saveListingPrice($listingId, $listingPrice);
    }
    if($imgUpload){
        saveMainListingPhoto($listingId, $newFileName.".".$imageFileType);
    }
    $roomsSubmited = array_map(function($elem){
        return $elem->roomId;
    }, $rooms);
    $listingRooms = getRoomsOfListing($listingId);
    foreach($listingRooms as $room){
        //If room in list of rooms submitted, update count
        if(in_array($room["room_type_id"], $roomsSubmited)){
            $count = 0;
            foreach($rooms as $key => $r){
                if($r->roomId == $room["room_type_id"]){
                    $count = $r->count;
                    //Remove rooms who's value is only being changed from submitted list of rooms
                    unset($rooms[$key]);
                    break;
                }
            }
            updateListingRoomCount($listingId, $room["room_type_id"], $count);
        }
        //If room not in the list of rooms submitted
        else{
            removeListingRoom($listingId, $room["room_type_id"]);
        }
    }
    //For every remaining submitted room
    foreach($rooms as $room){
        saveListingRoom($listingId, $room->roomId, $room->count);
    }
}
catch (PDOException $e){
    echoUnexpectedError($e);
}

if($imgUpload){
    move_uploaded_file($_FILES["listingPhoto"]["tmp_name"], $target_file);
}
$result["general"] = "Successfully edited listing";
http_response_code(201);
echo json_encode($result)
?>