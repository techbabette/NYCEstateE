<?php

function echoNoPermission(){
    http_response_code(404);
    die();
}
function isValidJSON($str) {
    json_decode($str);
    return json_last_error() == JSON_ERROR_NONE;
}
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

    $prepSt->execute();

}

function insertSingleParamater($table, $paramater, $paramaterName){
    include ("connection.php");

    $statement = "INSERT INTO $table ($paramaterName) 
    VALUES (?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $paramater);
    
    $prepSt->execute();
}

function getEverythingFromTable($table){
    include ("connection.php");

    $statement = "SELECT * FROM $table";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getEveryParamFromTable($table, $param){
    include ("connection.php");

    $statement = "SELECT $param FROM $table";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getEveryRowWhereParamFromTable($table, $param, $value){
    include ("connection.php");

    $statement = "SELECT $param FROM $table WHERE $param = ?";
    $prepSt = $conn->prepare($statement);
    $prepSt->bindParam(1, $value, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
?>