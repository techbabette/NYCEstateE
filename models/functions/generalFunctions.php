<?php
function echoUnprocessableEntity($message, $input = ""){
    $result["error"] = $message.$input;
    http_response_code(422);
    echo json_encode($result);
    die();
}
function echoImproperRequest($message = "All fields are required"){
    $result["error"] = $message;
    http_response_code(400);
    echo json_encode($result);
    die();
}
function echoUnexpectedError($e = "An unexpected error occured"){
    $result["error"] = $e;
    http_response_code(500);
    echo json_encode($result);
    die();
}
function echoUnauthorized($e = "You have to log in first"){
    $result["error"] = $e;
    http_response_code(401);
    echo json_encode($result);
    die();
}
function echoNoPermission($e = "You are not permitted this action"){
    $result["error"] = $e;
    http_response_code(403);
    echo json_encode($result);
    die();
}
function echoNotFound($e = "Not found"){
    $result["error"] = $e;
    http_response_code(404);
    echo json_encode($result);
    die(); 
}
function echoGone($e = "No longer exists"){
    $result["error"] = $e;
    http_response_code(410);
    echo json_encode($result);
    die(); 
}
function getUserLevel($id){
    include ("../../../connection.php");

    $statement = "SELECT user_id, level 
    FROM users u 
    INNER JOIN roles r ON u.role_id = r.role_id
    INNER JOIN accesslevels al ON r.access_level_id = al.access_level_id
    WHERE user_id = ? AND level > 0";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $id, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    if($prepSt->rowCount() > 0){
        return $result;
    }
    else{
        $result = array("user_id" => 0, "level" => 1);
        session_unset();
        return $result;
    }
}
function checkAccessLevel($requiredLevel, $e = "You are not permitted this action"){
    //If user not logged in, die and return 401
    if(!isset($_SESSION["user"])){
        echoUnauthorized($e);
    }

    //If user's access level is too low, die and return 403
    if(getUserLevel($_SESSION["user"]["user_id"])["level"] < $requiredLevel){
        echoNoPermission($e);
    }
}
function isValidJSON($str) {
    json_decode($str);
    return json_last_error() == JSON_ERROR_NONE;
}
function updateTextValue($table,$toChange, $toChangeValue, $paramater, $paramaterValue){
    include ("../../../connection.php");

    $statement = "UPDATE $table SET $toChange = ?
                  WHERE $paramater = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $toChangeValue);
    $prepSt->bindParam(2, $paramaterValue, PDO::PARAM_INT);

    $prepSt->execute(); 
}

function deleteSingleRow($table, $paramater, $paramaterValue){
    include ("../../../connection.php");

    $statement = "DELETE FROM $table WHERE $paramater = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $paramaterValue, PDO::PARAM_INT);

    $prepSt->execute();

}

function softDeleteSingleRow($table, $parameter, $parameterValue){
    include ("../../../connection.php");

    $statement = "UPDATE $table SET dateDeleted = NOW() WHERE $parameter = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $parameterValue, PDO::PARAM_INT);

    $prepSt->execute();
} 


function insertSingleParamater($table, $paramater, $paramaterName){
    include ("../../../connection.php");

    $statement = "INSERT INTO $table ($paramaterName) 
    VALUES (?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $paramater);
    
    $prepSt->execute();

    return $conn->lastInsertId();
}

function getEverythingFromTable($table){
    include ("../../../connection.php");

    $statement = "SELECT * FROM $table";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getEveryParamFromTable($table, $param){
    include ("../../../connection.php");

    $statement = "SELECT $param FROM $table";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getEveryRowWhereParamFromTable($table, $param, $value){
    include ("../../../connection.php");

    $statement = "SELECT $param FROM $table WHERE $param = ?";
    $prepSt = $conn->prepare($statement);
    $prepSt->bindParam(1, $value, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function addLineToFile($line, $file){
    $fileToAddTo = fopen("../../data/".$file.".txt", "a");

    fwrite($fileToAddTo, $line);

    fclose($fileToAddTo);
}
?>