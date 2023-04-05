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
function getAllLinks(){
    include ("connection.php");

    $statement = "SELECT l.link_id AS id, link_title, level_title, href, IF(landing, \"Main\", \"Pages\"), location, IFNULL((SELECT link_title FROM links WHERE link_id = l.parent_id),\"None\") AS Parent, IFNULL(icon, \"None\") AS Icon 
                  FROM links l
                  LEFT JOIN linkicons li ON l.link_id = li.link_id
                  INNER JOIN accesslevels a ON l.access_level_id = a.access_level_id
                  ORDER BY level desc
                  ";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $results = $prepSt->fetchAll();

    return $results;
}
function createNewLink($title, $href, $aLevel, $location, $landing){
    include ("connection.php");

    $statement = "INSERT INTO (link_title, href, access_level_id, location, landing) VALUES(?,?,?,?,?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $title);
    $prepSt->bindParam(2, $href);
    $prepSt->bindParam(3, $aLevel, PDO::PARAM_INT);
    $prepSt->bindParam(4, $location);
    $prepSt->bindParam(5, $landing, PDO::PARAM_INT);

    $results = $prepSt->execute();
    
    return $results;
}
function createNewLinkIcon($linkId, $icon){
    include ("connection.php");

    $statement = "INSERT INTO (link_id, icon) VALUES(?,?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $linkId, PDO::PARAM_INT);
    $prepSt->bindParam(2, $icon);

    $results = $prepSt->execute();

    return $results;
}
?>