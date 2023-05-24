<?php
function getLinks($accessLevel, $loggedIn){
    include ("../../../connection.php");

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
function getAllLinks($sort){
    include ("../../../connection.php");

    $statement = "SELECT l.link_id AS id, link_title, level_title, href, IF(landing, \"Root\", \"Pages\") as flocation, location, priority, IFNULL((SELECT link_title FROM links WHERE link_id = l.parent_id),\"None\") AS Parent, 
                  IFNULL((SELECT icon FROM linkicons WHERE link_id = l.link_id AND active = 1), \"None\") AS icon 
                  FROM links l
                  INNER JOIN accesslevels a ON l.access_level_id = a.access_level_id
                  ";
    $orderByStub = "ORDER BY ";

    if($sort == 0) $orderByStub.= " link_title DESC";
    if($sort == 1) $orderByStub.= " link_title ASC";

    if($sort == 2) $orderByStub.= " level_title DESC";
    if($sort == 3) $orderByStub.= " level_title ASC";

    if($sort == 4) $orderByStub.= " href DESC";
    if($sort == 5) $orderByStub.= " href ASC";

    if($sort == 6) $orderByStub.= " flocation DESC";
    if($sort == 7) $orderByStub.= " flocation ASC";

    if($sort == 8) $orderByStub.= " location DESC";
    if($sort == 9) $orderByStub.= " location ASC";

    if($sort == 10) $orderByStub.= " priority DESC";
    if($sort == 11) $orderByStub.= " priority ASC";

    if($sort == 12) $orderByStub.= " Parent DESC";
    if($sort == 13) $orderByStub.= " Parent ASC";

    if($sort == 14) $orderByStub.= " icon DESC";
    if($sort == 15) $orderByStub.= " icon ASC";

    $statement .= $orderByStub;
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $results = $prepSt->fetchAll();

    return $results;
}
function getSpecificLink($linkId){
    include ("../../../connection.php");

    $statement = "SELECT link_title as title, href, (SELECT icon FROM linkicons WHERE link_id = l.link_id AND active = 1) as icon, access_level_id, location, priority, landing FROM links l
                  WHERE l.link_id = :link_id";
    $prepSt = $conn->prepare($statement);
    $prepSt->bindParam("link_id", $linkId);

    $prepSt->execute();
    $results = $prepSt->fetch();

    return $results;
}
function createNewLink($title, $href, $aLevel, $location, $landing, $priority){
    include ("../../../connection.php");

    $statement = "INSERT INTO links (link_title, href, access_level_id, location, landing, priority) VALUES(?, ?, ?, ?, ?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $title);
    $prepSt->bindParam(2, $href);
    $prepSt->bindParam(3, $aLevel, PDO::PARAM_INT);
    $prepSt->bindParam(4, $location);
    $prepSt->bindParam(5, $landing, PDO::PARAM_INT);
    $prepSt->bindParam(6, $priority, PDO::PARAM_INT);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function editLink($linkId, $title, $href, $aLevel, $location, $priority, $landing){
    include ("../../../connection.php");

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
    include ("../../../connection.php");

    $statement = "INSERT INTO linkicons (link_id, icon) VALUES(?,?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $linkId, PDO::PARAM_INT);
    $prepSt->bindParam(2, $icon);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function getLinkIcon($link_id){
    include ("../../../connection.php");

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
    include ("../../../connection.php");

    $statement = "UPDATE linkicons SET active = 0 WHERE link_id = :linkId AND active = 1";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("linkId", $linkId);

    return $prepSt->execute();
}
?>