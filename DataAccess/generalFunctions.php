<?php
function echoNoPermission(){
    $result["error"] = "You are not permitted this action";
    http_response_code(404);
    echo json_encode($result);
    die();
}
function getUserLevel($id){
    include ("connection.php");

    $statement = "SELECT user_id, level 
    FROM users u 
    INNER JOIN roles r ON u.role_id = r.role_id
    INNER JOIN accesslevels al ON r.access_level_id = al.access_level_id
    WHERE user_id = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $id, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    if($prepSt->rowCount() > 0){
        return $result;
    }
    else{
        $result = array("user_id" => 0, "level" => 1);
        return $result;
    }
}
function checkAccessLevel($requiredLevel){
    // If user not logged in, die
    if(!isset($_SESSION["user"])){
        echoNoPermission();
    }

    //If user's access level is too low, die
    if(getUserLevel($_SESSION["user"]["user_id"])["level"] < $requiredLevel){
        echoNoPermission();
    }
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