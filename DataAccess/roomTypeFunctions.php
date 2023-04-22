<?php
function getAllRoomTypes(){
    require ("connection.php");

    $statement = "SELECT room_type_id AS id, room_name as title FROM roomtypes ORDER BY room_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getSpecificRoomType($id){
    require ("connection.php");

    $statement = "SELECT room_name AS title FROM roomtypes
                  WHERE room_type_id = :room_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("room_type_id", $id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetch();
}
?>