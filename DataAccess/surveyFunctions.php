<?php
function getAllQuestions(){
    include ("../../connection.php");

    $statement = "SELECT q.question_id, q.question, COUNT(ua.user_id) as Count FROM questions q
                  LEFT JOIN answers a ON a.question_id = q.question_id
                  LEFT JOIN useranswers ua ON a.answer_id = ua.answer_id
                  WHERE q.dateDeleted IS NOT NULL
                  GROUP BY q.question_id, q.question";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    $result = $prepSt->fetchAll();

    return $result;
}
function getQuestions($user_id){
    include ("../../connection.php");

    $statement = "SELECT q.question_id, q.question FROM questions q
                  WHERE q.dateDeleted IS NOT NULL AND q.question_id NOT IN (SELECT question_id FROM useranswers WHERE user_id = :user_id)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getQuestionAnswers($question_id){
    include ("../../connection.php");

    $statement = "SELECT a.answer_id, a.answer FROM answers a
                  INNER JOIN questions q ON a.question_id = q.question_id
                  WHERE q.question_id = :question_id
                  AND a.dateDeleted IS NULL";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $conn->fetchAll();
}
function saveQuestionAnswer($question_id, $text){
    include ("../../connection.php");

    $statement = "INSERT INTO answers (question_id, text) VALUES (:question_id, :text)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);
    $prepSt->bindParam("text", $text);
    
    $prepSt->execute();

    return $conn->lastInsertId();
}
function saveUserAnswer($answer_id, $user_id){
    include ("../../connection.php");

    $statement = "INSERT INTO useranswers (question_id, answer_id) VALUES (:question_id, :answer_id)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);
    $prepSt->bindParam("answer_id", $answer_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function editQuestion($question_id, $text){
    include ("../../connection.php");

    $statement = "UPDATE questions SET question = :text WHERE question_id = :question_id";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);
    $prepSt->bindParam("text", $text);

    return $prepSt->execute();
}
function editQuestionAnswer($answer_id, $text){
    include ("../../connection.php");

    $statement = "UPDATE answers SET answer = :text WHERE answer_id = :answer_id";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("answer_id", $answer_id, PDO::PARAM_INT);
    $prepSt->bindParam("text", $text);

    return $prepSt->execute();
}
function disableQuestionAnswer($answer_id){
    include ("../../connection.php");

    $statement = "UPDATE answers SET dateDeleted = NOW() WHERE answer_id = :answer_id";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("answer_id", $answer_id, PDO::PARAM_INT);

    return $prepSt->execute();
}
function disableQuestion($question_id){
    include ("../../connection.php");

    $statement = "UPDATE questions SET dateDeleted = NOW() WHERE question_id = :question_id";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);

    return $prepSt->execute();
}
?>