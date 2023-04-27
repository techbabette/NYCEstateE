<?php
function getAllMessageTypes(){
    include ("../../connection.php");

    $statement = "SELECT message_type_id AS id, message_type_name as title FROM messagetypes ORDER BY message_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllMessageTypesCount(){
    include ("../../connection.php");

    // $statement = "SELECT b.borough_id AS id, borough_name as title, COUNT(l.listing_id) as Count
    //               FROM boroughs b LEFT JOIN listings l ON b.borough_id = l.borough_id
    //               GROUP BY b.borough_id, borough_name
    //               ORDER BY COUNT(l.listing_id) DESC";

    $statement = "SELECT mt.message_type_id AS id, message_type_name AS title, COUNT(m.message_id) AS Count
                  FROM messagetypes mt LEFT JOIN messages m on mt.message_type_id = m.message_type_id 
                  GROUP BY mt.message_type_id, message_type_name
                  ORDER BY COUNT(m.message_id) DESC";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllMessages(){
    include ("../../connection.php");

    $statement = "SELECT m.message_id AS id, email, message_type_name, title, message, m.dateCreated
                  FROM messages m 
                  INNER JOIN messagetypes mt ON m.message_type_id = mt.message_type_id
                  INNER JOIN users u ON u.user_id = m.user_id
                  ORDER BY m.dateCreated DESC";
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