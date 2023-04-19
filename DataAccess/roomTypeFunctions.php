<?php
function getAllRoomTypes(){
    require ("connection.php");

    $statement = "SELECT room_type_id AS id, room_name as title FROM roomtypes ORDER BY room_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
?>