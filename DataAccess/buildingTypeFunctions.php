<?php
function getAllBuildingTypes(){
    require ("connection.php");

    $statement = "SELECT building_type_id AS id, type_name as title FROM buildingtypes ORDER BY type_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
?>