<?php
function getAllAccessLevels(){
    require ("connection.php");

    $statement = "SELECT access_level_id AS id, level_title as title FROM accesslevels ORDER BY level";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
?>