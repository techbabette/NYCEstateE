<?php
function getAllQuestions(){
    include ("../../connection.php");

    $statement = "SELECT q.question_id, q.question, COUNT(ua.user_id) as Count FROM questions q
                  LEFT JOIN answers a ON a.question_id = q.question_id
                  RIGHT JOIN useranswers ua ON a.answer_id = ua.answer_id
                  WHERE q.dateDeleted IS NOT NULL
                  GROUP BY q.question_id, q.question";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    $result = $prepSt->fetchAll();

    return $result;
}
?>