<?php
function getAllBoroughs(){
    require ("connection.php");

    $statement = "SELECT borough_id AS id, borough_name as title FROM boroughs ORDER BY borough_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllBoroughsCount(){
    require ("connection.php");

    $statement = "SELECT b.borough_id AS id, borough_name as title, COUNT(l.listing_id) as Count
                  FROM boroughs b LEFT JOIN listings l ON b.borough_id = l.borough_id
                  GROUP BY b.borough_id, borough_name
                  ORDER BY COUNT(*) DESC";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
?>