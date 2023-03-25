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

    $statement = "SELECT user_id, CONCAT(name, ' ', lastName) AS username, level 
    FROM users u 
    INNER JOIN roles r ON u.role_id = r.role_id
    INNER JOIN accesslevels al ON r.access_level_id = al.access_level_id
    WHERE email = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $email);

    $prepSt -> execute();
    $result = $prepSt->fetch();

    return $result;
}

function getAllUsers(){
    include ("connection.php");

    $statement = "SELECT user_id AS id, name, lastName, email, dateCreated, role_name
                  FROM users u
                  INNER JOIN roles r on u.role_id = r.role_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $result = $prepSt->fetchAll();

    return $result;
}
?>