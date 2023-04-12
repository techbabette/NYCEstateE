<?php
function getAllBoroughs(){
    require ("connection.php");

    $statement = "SELECT borough_id AS id, borough_name as title FROM boroughs ORDER BY borough_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
?>