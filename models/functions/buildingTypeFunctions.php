<?php
function getAllBuildingTypes(){
    include ("../../../connection.php");

    $statement = "SELECT building_type_id AS id, type_name as title FROM buildingtypes ORDER BY type_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllBuildingTypesCount($sort){
    include ("../../../connection.php");

    $statement = "SELECT bt.building_type_id AS id, type_name as title,  COUNT(l.listing_id) as Count
                  FROM buildingtypes bt LEFT JOIN listings l ON bt.building_type_id = l.building_type_id
                  GROUP BY bt.building_type_id, type_name";
    $orderByStub = " ORDER BY ";

    if($sort == 0) $orderByStub.= " type_name DESC";
    if($sort == 1) $orderByStub.= " type_name ASC";

    if($sort == 2) $orderByStub.= " COUNT(l.listing_id) DESC";
    if($sort == 3) $orderByStub.= " COUNT(l.listing_id) ASC";

    $statement.=$orderByStub;
    
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllBuildingTypesWithListings(){
    include ("../../../connection.php");

    $statement = "SELECT bt.building_type_id AS id, type_name as title,  COUNT(l.listing_id) as Count
                  FROM buildingtypes bt INNER JOIN listings l ON bt.building_type_id = l.building_type_id
                  WHERE l.dateDeleted IS NULL
                  GROUP BY bt.building_type_id, type_name
                  ORDER BY COUNT(l.listing_id) DESC";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getSpecificBuildingType($id){
    include ("../../../connection.php");

    $statement = "SELECT type_name AS title FROM buildingtypes
                  WHERE building_type_id = :building_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("building_type_id", $id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetch();
}
?>