<?php
function getLinks($accessLevel){
    include ("connection.php");

    $statement = "SELECT link_title, href, landing, location, parent_id, level
    FROM links l
    INNER JOIN accesslevels a ON l.access_level_id = a.access_level_id
    WHERE level <= ?";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $accessLevel, PDO::PARAM_INT);

    $prepSt->execute();
    $results = $prepSt->fetchAll();

    return $results;
}
?>