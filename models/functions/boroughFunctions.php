<?php
function getAllBoroughs(){
    include ("../../../connection.php");

    $statement = "SELECT borough_id AS id, borough_name as title FROM boroughs ORDER BY borough_name";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllBoroughsCount($sort){
    include ("../../../connection.php");

    $statement = "SELECT b.borough_id AS id, borough_name as title, COUNT(l.listing_id) as Count
                  FROM boroughs b LEFT JOIN listings l ON b.borough_id = l.borough_id
                  GROUP BY b.borough_id, borough_name";
    $orderByStub = " ORDER BY ";

    if($sort == 0) $orderByStub.= " borough_name DESC";
    if($sort == 1) $orderByStub.= " borough_name ASC";

    if($sort == 2) $orderByStub.= " COUNT(l.listing_id) DESC";
    if($sort == 3) $orderByStub.= " COUNT(l.listing_id) ASC";

    $statement.=$orderByStub;
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getAllBoroughsWithListings(){
    include ("../../../connection.php");

    $statement = "SELECT b.borough_id AS id, borough_name as title, COUNT(l.listing_id) as Count
                  FROM boroughs b INNER JOIN listings l ON b.borough_id = l.borough_id
                  WHERE l.dateDeleted IS NULL
                  GROUP BY b.borough_id, borough_name
                  ORDER BY COUNT(l.listing_id) DESC";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();

    return $prepSt->fetchAll();
}
function getSpecificBorough($id){
    include ("../../../connection.php");

    $statement = "SELECT borough_name AS title FROM boroughs
                  WHERE borough_id = :borough_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("borough_id", $id, PDO::PARAM_INT);

    $prepSt->execute();

    return $prepSt->fetch();
}
?>