<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if(!isset($_POST["id"])){
    echoImproperRequest("Must provide a valid listing id");
}

$id = $_POST["id"];

$exists = count(getEveryRowWhereParamFromTable("listings", "listing_id", $id)) > 0;

if(!$exists){
    echoUnprocessableEntity("Provided listing id does not exist");
}

require("../functions/listingFunctions.php");

try{
    restoreListing($id);
}
catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully restored listing"; 
http_response_code(200);
echo json_encode($result);
?>