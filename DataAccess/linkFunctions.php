<?php
function getLinks($accessLevel, $loggedIn){
    include ("connection.php");

    $statement = "SELECT link_title, href, landing, location, parent_id, level, icon
    FROM links l
    LEFT JOIN linkicons li ON l.link_id = li.link_id
    INNER JOIN accesslevels a ON l.access_level_id = a.access_level_id
    WHERE level <= ?";

    if($loggedIn){
        $statement.= " AND level <> 0";
    }

    $statement.=" ORDER BY l.link_id";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $accessLevel, PDO::PARAM_INT);

    $prepSt->execute();
    $results = $prepSt->fetchAll();

    return $results;
}
?>