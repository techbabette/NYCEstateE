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

if(!isset($_POST["questionName"])
|| !isset($_POST["questionAnswers"]))
{
    echoUnprocessableEntity("All fields are required");
}

$questionName = $_POST["questionName"];
$questionAnswers = $_POST["questionAnswers"];

$reQuestion = '/^[A-Z][a-z\']{1,20}(\s[A-Za-z][a-z\']{0,20}){2,10}$/';
$reAnswer = '/^[A-Z][a-z\']{0,20}(\s[A-Za-z][a-z\']{0,20}){2,10}$/';

if(!preg_match($reQuestion, $questionName)){
    echoUnprocessableEntity("Question does not match format (Between three and eleven words)");
}

foreach($questionAnswers as $answer){
    if(!preg_match($reAnswer, $answer)){
        echoUnprocessableEntity("Answer does not match format (Between three and eleven words)");
    }
}
require("../DataAccess/surveyFunctions.php");
try{
    $newQuestion = saveQuestion($questionName);
    foreach($questionAnswers as $answer){
        saveQuestionAnswer($newQuestion, $answer);
    }
}
catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully created new question";
http_response_code(201);
echo json_encode($result)
?>