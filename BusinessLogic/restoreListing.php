<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if(!isset($_POST["id"])){
    echoUnprocessableEntity("Must provide a valid listing id");
}

$id = $_POST["id"];

$exists = count(getEveryRowWhereParamFromTable("listings", "listing_id", $id)) > 0;

if(!$exists){
    echoUnprocessableEntity("Provided listing id does not exist");
}

require("../DataAccess/listingFunctions.php");

try{
    restoreListing($id);
    $result["general"] = "Successfully restored listing"; 
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError($e);
}