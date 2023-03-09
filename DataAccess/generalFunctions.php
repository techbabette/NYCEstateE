<?php
function updateTextValue($table,$toChange, $toChangeValue, $paramater, $paramaterValue){
    include ("connection.php");

    $statement = "UPDATE $table SET $toChange = ?
                  WHERE $paramater = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $toChangeValue);
    $prepSt->bindParam(2, $paramaterValue, PDO::PARAM_INT);

    $prepSt->execute(); 
}

function deleteSingleRow($table, $paramater, $paramaterValue){
    include ("connection.php");

    $statement = "DELETE FROM $table WHERE $paramater = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $paramaterValue, PDO::PARAM_INT);
}

?>