<?php
function encryptPassword($password){
    return password_hash($password, PASSWORD_DEFAULT);
}
function createNewUser($email, $password, $name, $lastName){
    include ("../../../connection.php");

    $crypted = encryptPassword($password);

    $statement = "INSERT INTO users (email, password, name, lastName) 
                  VALUES (?, ?, ?, ?)";
    $prepSt = $conn->prepare($statement);
    
    $prepSt->bindParam(1, $email);
    $prepSt->bindParam(2, $crypted);
    $prepSt->bindParam(3, $name);
    $prepSt->bindParam(4, $lastName);

    $prepSt->execute(); 

    return $conn->lastInsertId();
}
function editUser($userId, $email, $name, $lastName, $role){
    include ("../../../connection.php");

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
    include ("../../../connection.php");

    $crypted = encryptPassword($password);

    $statement = "UPDATE users SET password = :password
                  WHERE user_id = :userId";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("userId", $userId, PDO::PARAM_INT);
    $prepSt->bindParam("password", $crypted);

    return $prepSt->execute();
}
function checkIfEmailInUse($email){
    include ("../../../connection.php");

    $statement = "SELECT email FROM users WHERE email = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $email);

    $prepSt->execute();
    $result = $prepSt->rowCount() != 0;

    return $result;
}

function attemptLogin($email, $password){
    include ("../../../connection.php");

    $statement = "SELECT password, user_id FROM users WHERE email = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $email);

    $prepSt->execute();

    $user = $prepSt->fetch();

    if(!$user){
        return 0;
    }

    $userId = $user["user_id"];

    $encryptedPassword = $user["password"];

    if(password_verify($password, $encryptedPassword)){
        return $userId;
    }
    else{
        return -$userId;
    }
}

function getUserInformation($id){
    include ("../../../connection.php");

    $statement = "SELECT user_id, CONCAT(name, ' ', lastName) AS username, level 
    FROM users u 
    INNER JOIN roles r ON u.role_id = r.role_id
    INNER JOIN accesslevels al ON r.access_level_id = al.access_level_id
    WHERE user_id = ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $id, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    return $result;
}

function getAllUsers($sort = -1){
    include ("../../../connection.php");

    $statement = "SELECT user_id AS id, name, lastName, email, dateCreated, role_name
                  FROM users u
                  INNER JOIN roles r on u.role_id = r.role_id";
    $orderByStub = " ORDER BY";


    if($sort == 0) $orderByStub.= " name DESC";
    if($sort == 1) $orderByStub.= " name ASC";

    if($sort == 2) $orderByStub.= " lastName DESC";
    if($sort == 3) $orderByStub.= " lastName ASC";

    if($sort == 4) $orderByStub.= " dateCreated DESC";
    if($sort == 5) $orderByStub.= " dateCreated ASC";

    if($sort == 6) $orderByStub.= " role_name DESC";
    if($sort == 7) $orderByStub.= " role_name ASC";

    if($sort == 8) $orderByStub.= " email DESC";
    if($sort == 9) $orderByStub.= " email ASC";

    if($sort == -1) $orderByStub.= " r.role_id DESC";

    $statement .= $orderByStub;

    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $result = $prepSt->fetchAll();

    return $result;
}

function getSpecificUser($userId){
    include ("../../../connection.php");

    $statement = "SELECT name, lastName, email, role_id FROM users WHERE user_id = :user_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $userId);

    $prepSt->execute();

    return $prepSt->fetch();
}

function getAllUserRoles(){
    include ("../../../connection.php");

    $statement = "SELECT role_id AS id, role_name as title FROM roles ORDER BY id";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}

function createActivationLink($userId){
    include ("../../../connection.php");

    $link = md5(uniqid(rand())).md5(time()).md5(uniqid(rand()));

    $statement = "INSERT INTO activationlinks(activation_link, user_id) VALUES(:link, :userId)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(":link", $link);
    $prepSt->bindParam(":userId", $userId, PDO::PARAM_INT);

    $prepSt ->execute();

    return $link;
}

function getUserFromActivationLink($activationLink){
    include ("../../../connection.php");

    $statement = "SELECT u.user_id, email FROM users u
                  INNER JOIN activationlinks al ON u.user_id = al.user_id
                  WHERE al.activation_link = :activationLink";
              
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(":activationLink", $activationLink);

    $prepSt->execute();

    return $prepSt->fetch();
}

function deleteActivationLink($activationLink){
    include ("../../../connection.php");

    $statement = "DELETE FROM activationlinks
                  WHERE activation_link = :activation_link";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(":activationLink", $activationLink);

    $prepSt->execute();

    return $prepSt->fetch();
}

function activateUser($userId){
    include ("../../../connection.php");

    $statement = "UPDATE users
                  SET role_id = 1
                  WHERE user_id = :user_id";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(":user_id", $userId);

    $prepSt->execute();

    return 1;
}
?>