<?php
session_start();
$requiredLevel = 2;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel, "You must be logged in to add listings to favorites");

$result;

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

if(!isset($_POST["listingId"])){
    echoUnprocessableEntity("All fields are required");
}

$userId = $_SESSION["user"]["user_id"];
$listingId = $_POST["listingId"];

require("../DataAccess/favoriteFunctions.php");

$alreadyFavorite = checkIfAlreadyFavorite($userId, $listingId);

if($alreadyFavorite){
    echoUnprocessableEntity("Cannot favorite listing more than once");
}

try{
    saveUserFavorite($userId, $listingId);
    $result["general"] = "Successfully added listing to favorites";
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}