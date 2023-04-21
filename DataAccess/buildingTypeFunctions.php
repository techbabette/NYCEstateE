<?php
function getAllBuildingTypes(){
    require ("connection.php");

    $statement = "SELECT building_type_id AS id, type_name as title FROM buildingtypes ORDER BY type_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllBuildingTypesCount(){
    require ("connection.php");

    $statement = "SELECT bt.building_type_id AS id, type_name as title,  COUNT(l.listing_id) as Count
                  FROM buildingtypes bt LEFT JOIN listings l ON bt.building_type_id = l.building_type_id
                  GROUP BY bt.building_type_id, type_name
                  ORDER BY COUNT(*) DESC";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
?>