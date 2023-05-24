<?php
function getAllMessageTypes(){
    include ("../../connection.php");

    $statement = "SELECT message_type_id AS id, message_type_name as title FROM messagetypes ORDER BY message_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllMessageTypesCount($sort){
    include ("../../connection.php");

    $statement = "SELECT mt.message_type_id AS id, message_type_name AS title, COUNT(m.message_id) AS Count
                  FROM messagetypes mt LEFT JOIN messages m on mt.message_type_id = m.message_type_id 
                  GROUP BY mt.message_type_id, message_type_name";
    $orderByStub = " ORDER BY";

    if($sort == 0) $orderByStub.= " message_type_name DESC";
    if($sort == 1) $orderByStub.= " message_type_name ASC";

    if($sort == 2) $orderByStub.= " COUNT(m.message_id) DESC";
    if($sort == 3) $orderByStub.= " COUNT(m.message_id) ASC";

    $statement.=$orderByStub;

    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllMessages($sort){
    include ("../../connection.php");

    $statement = "SELECT m.message_id AS id, email, message_type_name, title, message, m.dateCreated
                  FROM messages m 
                  INNER JOIN messagetypes mt ON m.message_type_id = mt.message_type_id
                  INNER JOIN users u ON u.user_id = m.user_id";
    $orderByStub = " ORDER BY";

    if($sort == 0) $orderByStub.= " email DESC";
    if($sort == 1) $orderByStub.= " email ASC";

    if($sort == 2) $orderByStub.= " message_type_name DESC";
    if($sort == 3) $orderByStub.= " message_type_name ASC";

    if($sort == 4) $orderByStub.= " title DESC";
    if($sort == 5) $orderByStub.= " title ASC";

    if($sort == 6) $orderByStub.= " message DESC";
    if($sort == 7) $orderByStub.= " message ASC";

    if($sort == 8) $orderByStub.= " m.dateCreated DESC";
    if($sort == 9) $orderByStub.= " m.dateCreated ASC";

    if($sort == -1) $orderByStub.= " m.dateCreated DESC";

    $statement.=$orderByStub;

    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getSpecificMessageType($id){
    include ("../../connection.php");

    $statement = "SELECT message_type_name AS title FROM messagetypes
                  WHERE message_type_id = :message_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("message_type_id", $id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetch();
}
function createNewMessage($user_id, $message_type_id, $title, $message){
    include ("../../connection.php");

    $statement = "INSERT INTO messages (user_id, message_type_id, title, message) VALUES (:user_id, :message_type_id, :title, :message)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);
    $prepSt->bindParam("message_type_id", $message_type_id, PDO::PARAM_INT);
    $prepSt->bindParam("title", $title);
    $prepSt->bindParam("message", $message);

    return $prepSt->execute();
}
function createNewMessageType($message_type_name){
}
?>