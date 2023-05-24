<?php
function getAllRoomTypes($sort){
    include ("../../../connection.php");

    $statement = "SELECT room_type_id AS id, room_name as title FROM roomtypes";
    $orderByStub = " ORDER BY ";
    
    if($sort == 0) $orderByStub.= " room_name DESC";
    if($sort == 1) $orderByStub.= " room_name ASC";

    $statement.=$orderByStub;

    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getSpecificRoomType($id){
    include ("../../../connection.php");

    $statement = "SELECT room_name AS title FROM roomtypes
                  WHERE room_type_id = :room_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("room_type_id", $id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetch();
}
?>