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

if(!isset($_POST["questionId"])){
    echoImproperRequest("Must provide a valid question id");
}

$questionId = $_POST["questionId"];

require("../functions/surveyFunctions.php");

try{
    $questionExists = getSpecificQuestion($questionId);
    if(!$questionExists){
        echoNotFound();
    }
    $question["name"] = $questionExists["question"];
    $question["answers"] = getQuestionAnswers($questionId);
    $result["general"] = $question;
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}
?>