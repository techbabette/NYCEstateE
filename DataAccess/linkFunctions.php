<?php
function getLinks($accessLevel, $loggedIn){
    include ("../../connection.php");

    $statement = "SELECT link_title, href, landing, location, parent_id, level, (SELECT icon FROM linkicons WHERE link_id = l.link_id AND active = 1) as icon
    FROM links l
    INNER JOIN accesslevels a ON l.access_level_id = a.access_level_id
    WHERE level <= ?";

    if($loggedIn){
        $statement.= " AND level <> 0";
    }

    $statement.=" ORDER BY l.priority DESC";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $accessLevel, PDO::PARAM_INT);

    $prepSt->execute();
    $results = $prepSt->fetchAll();

    return $results;
}
function getAllLinks(){
    include ("../../connection.php");

    $statement = "SELECT l.link_id AS id, link_title, level_title, href, IF(landing, \"Root\", \"Pages\"), location, priority, IFNULL((SELECT link_title FROM links WHERE link_id = l.parent_id),\"None\") AS Parent, 
                  IFNULL((SELECT icon FROM linkicons WHERE link_id = l.link_id AND active = 1), \"None\") AS icon 
                  FROM links l
                  INNER JOIN accesslevels a ON l.access_level_id = a.access_level_id
                  ORDER BY location DESC, priority DESC 
                  ";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $results = $prepSt->fetchAll();

    return $results;
}
function getSpecificLink($linkId){
    include ("../../connection.php");

    $statement = "SELECT link_title as title, href, (SELECT icon FROM linkicons WHERE link_id = l.link_id AND active = 1) as icon, access_level_id, location, priority, landing FROM links l
                  WHERE l.link_id = :link_id";
    $prepSt = $conn->prepare($statement);
    $prepSt->bindParam("link_id", $linkId);

    $prepSt->execute();
    $results = $prepSt->fetchAll();

    return $results;
}
function createNewLink($title, $href, $aLevel, $location, $landing){
    include ("../../connection.php");

    $statement = "INSERT INTO links (link_title, href, access_level_id, location, landing) VALUES(?, ?, ?, ?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $title);
    $prepSt->bindParam(2, $href);
    $prepSt->bindParam(3, $aLevel, PDO::PARAM_INT);
    $prepSt->bindParam(4, $location);
    $prepSt->bindParam(5, $landing, PDO::PARAM_INT);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function editLink($linkId, $title, $href, $aLevel, $location, $priority, $landing){
    include ("../../connection.php");

    $statement = "UPDATE links SET link_title = :title, href = :href, access_level_id = :aLevel, location = :location, priority = :priority, landing = :landing
                  WHERE link_id = :linkId";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("title", $title);
    $prepSt->bindParam("href", $href);
    $prepSt->bindParam("aLevel", $aLevel, PDO::PARAM_INT);
    $prepSt->bindParam("location", $location);
    $prepSt->bindParam("priority", $priority, PDO::PARAM_INT);
    $prepSt->bindParam("landing", $landing, PDO::PARAM_INT);
    $prepSt->bindParam("linkId", $linkId, PDO::PARAM_INT);

    $return = $prepSt->execute();

    return $return;
}
function createNewLinkIcon($linkId, $icon){
    include ("../../connection.php");

    $statement = "INSERT INTO linkicons (link_id, icon) VALUES(?,?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $linkId, PDO::PARAM_INT);
    $prepSt->bindParam(2, $icon);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function getLinkIcon($link_id){
    include ("../../connection.php");

    $statement = "SELECT icon FROM linkicons 
                  WHERE link_id = :link_id AND active = 1
                  ORDER BY dateCreated DESC 
                  LIMIT 1";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("link_id", $link_id, PDO::PARAM_INT);

    $prepSt->execute();

    $result = $prepSt->fetch();

    if($result){
        return $result["icon"];
    }
    else{
        return "";
    }
}
function removeAllLinkIcons($linkId){
    include ("../../connection.php");

    $statement = "UPDATE linkicons SET active = 0 WHERE link_id = :linkId AND active = 1";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("linkId", $linkId);

    return $prepSt->execute();
}
?>