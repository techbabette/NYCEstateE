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
    echoImproperRequest("Must provide a valid question id");
}

$id = $_POST["id"];

$exists = count(getEveryRowWhereParamFromTable("questions", "question_id", $id)) > 0;

if(!$exists){
    echoUnprocessableEntity("Provided question id does not exist");
}

require("../DataAccess/surveyFunctions.php");

try{
    restoreQuestion($id);
}
catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully restored question"; 
http_response_code(200);
echo json_encode($result);