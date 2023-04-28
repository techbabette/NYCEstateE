<?php
function getAllQuestions(){
    include ("../../connection.php");

    $statement = "SELECT q.question_id AS id, q.question, COUNT(ua.user_id) as Count FROM questions q
                  LEFT JOIN answers a ON a.question_id = q.question_id
                  LEFT JOIN useranswers ua ON a.answer_id = ua.answer_id
                  WHERE q.dateDeleted IS NULL
                  GROUP BY q.question_id, q.question";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    $result = $prepSt->fetchAll();

    return $result;
}
function getAllDeletedQuestions(){
    include ("../../connection.php");

    $statement = "SELECT q.question_id AS id, q.question, COUNT(ua.user_id) as Count FROM questions q
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

    $statement = "SELECT q.question_id as id, q.question FROM questions q
                  WHERE q.dateDeleted IS NULL AND q.question_id NOT IN 
                  (SELECT question_id FROM useranswers ua INNER JOIN answers a ON ua.answer_id = a.answer_id  WHERE user_id = :user_id)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getSpecificQuestion($question_id){
    include ("../../connection.php");

    $statement = "SELECT question FROM questions
                  WHERE question_id = :question_id";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetch();
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

    return $prepSt->fetchAll();
}
function getQuestionAnswersCount($question_id){
    include ("../../connection.php");

    $statement = "SELECT a.answer, COUNT(ua.useranswer_id) AS count
                  FROM answers a
                  LEFT JOIN useranswers ua ON a.answer_id = ua.answer_id
                  WHERE a.question_id = :question_id
                  GROUP BY a.answer_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getQuestionAnswerIds($question_id){
    include ("../../connection.php");

    $statement = "SELECT a.answer_id FROM answers a
                  INNER JOIN questions q ON a.question_id = q.question_id
                  WHERE q.question_id = :question_id
                  AND a.dateDeleted IS NOT NULL";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $conn->fetchAll();
}
function saveQuestion($text){
    include ("../../connection.php");

    $statement = "INSERT INTO questions (question) VALUES (:text)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("text", $text);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function saveQuestionAnswer($question_id, $text){
    include ("../../connection.php");

    $statement = "INSERT INTO answers (question_id, answer) VALUES (:question_id, :text)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);
    $prepSt->bindParam("text", $text);
    
    $prepSt->execute();

    return $conn->lastInsertId();
}
function saveUserAnswer($user_id, $answer_id){
    include ("../../connection.php");

    $statement = "INSERT INTO useranswers (user_id, answer_id) VALUES (:user_id, :answer_id)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("answer_id", $answer_id, PDO::PARAM_INT);
    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);

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
function restoreQuestion($question_id){
    include ("../../connection.php");

    $statement = "UPDATE questions SET dateDeleted = NULL
                  WHERE question_id = :question_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("question_id", $question_id, PDO::PARAM_INT);

    return $prepSt->execute();
}
function checkIfUserAllowedToAnswer($user_id, $answer_id){
    include ("../../connection.php");

    $statement = "SELECT u.user_id FROM users u
                  INNER JOIN useranswers ua ON u.user_id = ua.user_id
                  INNER JOIN answers a ON ua.answer_id = a.answer_id
                  INNER JOIN questions q ON a.question_id = q.question_id
                  WHERE a.question_id IN (SELECT question_id FROM answers WHERE answer_id = :answer_id)
                  AND ua.user_id = :user_id
                  AND a.dateDeleted IS NULL
                  AND q.dateDeleted IS NULL";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);
    $prepSt->bindParam("answer_id", $answer_id, PDO::PARAM_INT);

    $prepSt->execute();

    $hasRows = $prepSt->fetch();

    if($hasRows){
        return false;
    }
    return true;
}
?>