<?php
function getAllBuildingTypes(){
    include ("../../connection.php");

    $statement = "SELECT building_type_id AS id, type_name as title FROM buildingtypes ORDER BY type_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllBuildingTypesCount(){
    include ("../../connection.php");

    $statement = "SELECT bt.building_type_id AS id, type_name as title,  COUNT(l.listing_id) as Count
                  FROM buildingtypes bt LEFT JOIN listings l ON bt.building_type_id = l.building_type_id
                  WHERE l.dateDeleted IS NULL
                  GROUP BY bt.building_type_id, type_name
                  ORDER BY COUNT(*) DESC";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getSpecificBuildingType($id){
    include ("../../connection.php");

    $statement = "SELECT type_name AS title FROM buildingtypes
                  WHERE building_type_id = :building_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("building_type_id", $id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetch();
}
?>