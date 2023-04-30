<?php
session_start();
$requiredLevel = 2;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

require("../DataAccess/surveyFunctions.php");

try{
    $questions = getQuestions($_SESSION["user"]["user_id"]);
    $result["general"] = array();
    foreach($questions as $question){
        $questionWithAnswer["question"] = $question;
        $questionWithAnswer["answers"] = getQuestionAnswers($question["id"]);
        array_push($result["general"], $questionWithAnswer);
    }
    http_response_code(200);
    echo json_encode($result);
}
catch (PDOException $e){
    echoUnexpectedError();
}