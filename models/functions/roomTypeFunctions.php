<?php
function getAllRoomTypes(){
    include ("../../../connection.php");

    $statement = "SELECT room_type_id AS id, room_name as title FROM roomtypes";

    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllRoomTypesCount($sort, $page, $perPage){
    include ("../../../connection.php");

    $statement = "SELECT rt.room_type_id AS id, room_name as title, COUNT(lr.listing_id) AS Count
                  FROM roomtypes rt LEFT JOIN listingrooms lr ON rt.room_type_id = lr.room_type_id
                  GROUP BY rt.room_type_id, room_name";
    $orderByStub = " ORDER BY ";
    
    if($sort == 0) $orderByStub.= " room_name DESC";
    if($sort == 1) $orderByStub.= " room_name ASC";

    if($sort == 2) $orderByStub.= " COUNT DESC";
    if($sort == 3) $orderByStub.= " COUNT ASC";

    $statement.=$orderByStub;

    $numberToSkip = ($page - 1) * $perPage;
    
    $statement .= 
    "
     LIMIT :numberToSkip,:perPage
    ";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("numberToSkip", $numberToSkip, PDO::PARAM_INT);
    $prepSt->bindParam("perPage", $perPage, PDO::PARAM_INT);

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