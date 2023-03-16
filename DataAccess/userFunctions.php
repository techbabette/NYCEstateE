<?php
function createNewUser($email, $password, $name, $lastName){
    include ("connection.php");

    $crypted = md5($password);

    $statement = "INSERT INTO users (email, password, name, lastName) 
                  VALUES (?, ?, ?, ?)";
    $prepSt = $conn->prepare($statement);
    
    $prepSt->bindParam(1, $email);
    $prepSt->bindParam(2, $crypted);
    $prepSt->bindParam(3, $name);
    $prepSt->bindParam(4, $lastName);

    $prepSt->execute(); 
}

function checkIfEmailInUse($email){
    include ("connection.php");

    $statement = "SELECT email FROM users WHERE email = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $email);

    $prepSt->execute();
    $result = $prepSt->rowCount() != 0;

    return $result;
}

function attemptLogin($email, $password){
    include ("connection.php");

    $crypted = md5($password);

    $statement = "SELECT email FROM users WHERE email = ? AND password = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $email);
    $prepSt->bindParam(2, $password);

    $prepSt->execute();
    $result = $prepSt->rowCount() != 0;

    return $result;
}

function getUserInformation($email){
    include ("connection.php");

    $statement = "SELECT user_id, name + ` ` lastName, level 
    FROM users u 
    INNER JOIN roles r WHERE u.role_id = r.role_id
    INNER JOIN accesslevels al WHERE r.access_level_id = al.access_level_id";
    $prepSt = $conn->prepare($statement);

    $prepSt -> execute();
    $result = $prepSt->fetch();

    return $result;
}
?>