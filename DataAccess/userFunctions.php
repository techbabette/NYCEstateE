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
function editUser($userId, $email, $name, $lastName, $role){
    include ("connection.php");

    $statement = "UPDATE users SET email = :email, name = :name, lastName = :lastName, role_id = :role
                  WHERE user_id = :userId";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("email", $email);
    $prepSt->bindParam("name", $name);
    $prepSt->bindParam("lastName", $lastName);
    $prepSt->bindParam("role", $role, PDO::PARAM_INT);
    $prepSt->bindParam("userId", $userId, PDO::PARAM_INT);
    
    return $prepSt->execute();
}
function editUserPassword($userId, $password){
    include ("connection.php");

    $crpyted = md5($password);

    $statement = "UPDATE users SET password = :password
                  WHERE user_id = :userId";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("userId", $userId, PDO::PARAM_INT);
    $prepSt->bindParam("password", $crpyted);

    return $prepSt->execute();
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