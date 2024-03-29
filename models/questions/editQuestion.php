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

if
(!isset($_POST["questionId"]) 
|| !isset($_POST["questionName"])
|| !isset($_POST["modifiedAnswers"])
)
{
    echoImproperRequest("All fields are required");
}

$questionId = $_POST["questionId"];
$questionName = $_POST["questionName"];

$reQuestion = '/^[A-Z][a-z\']{1,20}(\s[A-Za-z][a-z\']{0,20}){2,10}$/';
$reAnswer = '/^[A-Z][a-z\']{0,20}(\s[A-Za-z][a-z\']{0,20}){2,10}$/';

if(!preg_match($reQuestion, $questionName)){
    echoUnprocessableEntity("Question does not match format (Between three and eleven words)");
}

$newAnswers = array();
if(isset($_POST["newAnswers"])){
    $newAnswers = $_POST["newAnswers"];
}

$remainingAnswers = array();
if(isset($_POST["modifiedAnswers"])){
    $remainingAnswers = $_POST["modifiedAnswers"];
}

if(count($newAnswers) + count($remainingAnswers) < 2){
    echoUnprocessableEntity("Question must have at least two answers active at once");
}

foreach($newAnswers as $answer){
    if(!preg_match($reAnswer, $answer["text"])){
        echoUnprocessableEntity("Answer does not match format (Between three and eleven words)");
    }
}

foreach($remainingAnswers as $answer){
    if(!preg_match($reAnswer, $answer["text"])){
        echoUnprocessableEntity("Answer does not match format (Between three and eleven words)");
    }
}

require("../functions/surveyFunctions.php");
try{
    //Change question text
    editQuestion($questionId, $questionName);
    $existingAnswers = getQuestionAnswers($questionId);
    $remainingAnswerIds = array_map(function($elem)
    {
        return $elem["answerId"];
    }, $remainingAnswers);
    //For every answer found in database
    foreach($existingAnswers as $answer){
        //If not among the list of remaining answer ids, disable question 
        if(!in_array($answer["answer_id"], $remainingAnswerIds)){
            disableQuestionAnswer($answer["answer_id"]);
            continue;
        }
        /*For every remaining answer from database, if answer text is different
         disable current answer and create new one (so no text is overriden) */
        else{
            $text = "";
            foreach($remainingAnswers as $key => $mA){
                if($mA["answerId"] == $answer["answer_id"]){
                    $text = $mA["text"];
                    unset($remainingAnswers[$key]);
                }
            }
            //If new text is different from database text, disable current answer and create new one
            if($text != $answer["answer"]){
                disableQuestionAnswer($answer["answer_id"]);
                saveQuestionAnswer($questionId, $text);
            }
        }
    }
    //For every new answer, insert new row
    foreach($newAnswers as $answer){
        saveQuestionAnswer($questionId, $answer["text"]);
    }
}
catch (PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully edited question";
http_response_code(200);
echo json_encode($result)
?>