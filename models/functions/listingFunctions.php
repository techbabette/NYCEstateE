<?php
function createNewListing($borough, $building_type, $name, $description, $address, $size){
    include ("../../../connection.php");

    $statement = "INSERT INTO listings (borough_id, building_type_id, listing_name, description, address, size) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $prepSt = $conn->prepare($statement);
    
    $prepSt->bindParam(1, $borough);
    $prepSt->bindParam(2, $building_type);
    $prepSt->bindParam(3, $name);
    $prepSt->bindParam(4, $description);
    $prepSt->bindParam(5, $address);
    $prepSt->bindParam(6, $size);

    $prepSt->execute(); 

    $last_id = $conn->lastInsertId();
    
    return $last_id;
}

function editListing($listing_id, $borough_id, $building_type_id, $name, $description, $address, $size){
    include ("../../../connection.php");

    $statement = "UPDATE listings SET borough_id = :borough_id, building_type_id = :building_type_id, listing_name = :name, 
                  description = :description, address = :address, size = :size
                  WHERE listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);
    $prepSt->bindParam("borough_id", $borough_id, PDO::PARAM_INT);
    $prepSt->bindParam("building_type_id", $building_type_id, PDO::PARAM_INT);
    $prepSt->bindParam("name", $name);
    $prepSt->bindParam("description", $description);
    $prepSt->bindParam("address", $address);
    $prepSt->bindParam("size", $size);

    return $prepSt->execute();
}

function saveListingPrice($listing, $price){
    include ("../../../connection.php");

    $statement = "INSERT INTO listingprices (listing_id, price) VALUES (?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $listing, PDO::PARAM_INT);
    $prepSt->bindParam(2, $price);

    $result = $prepSt->execute();

    return $result;
}

function saveMainListingPhoto($listing, $path){
    include ("../../../connection.php");

    $main = true;

    $statement = "INSERT INTO listingphotos (listing_id, path, main) VALUES (?, ?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $listing, PDO::PARAM_INT);
    $prepSt->bindParam(2, $path);
    $prepSt->bindParam(3, $main);

    $result = $prepSt->execute();

    return $result;
}

function saveListingRoom($listing, $room, $count){
    include ("../../../connection.php");

    $statement = "INSERT INTO listingrooms (listing_id, room_type_id, numberOf) VALUES (?, ?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $listing, PDO::PARAM_INT);
    $prepSt->bindParam(2, $room, PDO::PARAM_INT);
    $prepSt->bindParam(3, $count, PDO::PARAM_INT);

    $result = $prepSt->execute();

    return $result;
}

function updateListingRoomCount($listing_id, $room_type_id, $numberOf){
    include ("../../../connection.php");

    $statement = "UPDATE listingrooms SET numberOf = :numberOf
                  WHERE listing_id = :listing_id AND room_type_id = :room_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);
    $prepSt->bindParam("room_type_id", $room_type_id, PDO::PARAM_INT);
    $prepSt->bindParam("numberOf", $numberOf, PDO::PARAM_INT);

    return $prepSt->execute();
}

function removeListingRoom($listing_id, $room_type_id){
    include ("../../../connection.php");

    $statement = "DELETE FROM listingrooms
                  WHERE listing_id = :listing_id AND room_type_id = :room_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);
    $prepSt->bindParam("room_type_id", $room_type_id, PDO::PARAM_INT);

    return $prepSt->execute();
}

function updateMainListingPhoto($listing, $path){
    include ("../../../connection.php");

    $statement = "UPDATE listingphotos SET path = ? WHERE listing_id = ? AND main = true";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $path);
    $prepSt->bindParam(2, $listing, PDO::PARAM_INT);

    $result = $prepSt->execute();

    return $result;
}

function getCurrentMainListingPhoto($listing){
    include ("../../../connection.php");

    $statement = "SELECT path FROM listingphotos 
                  WHERE main = 1 AND listing_id = :listing_id
                  ORDER BY dateUploaded DESC
                  LIMIT 1";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    return $result;
}

function getRoomsOfListing($listing){
    include ("../../../connection.php");

    $statement = "SELECT rt.room_name, rt.room_type_id, lr.numberOf 
                  FROM roomtypes rt 
                  INNER JOIN listingrooms lr ON rt.room_type_id = lr.room_type_id
                  INNER JOIN listings li ON lr.listing_id = li.listing_id
                  WHERE li.listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetchAll();

    return $result;
}

function getSpecificListing($listing){
    include ("../../../connection.php");

    $statement = "SELECT listing_name, description, (SELECT price FROM listingprices WHERE listing_id = :listing_id ORDER BY date DESC LIMIT 1) as price, size, address, borough_id, building_type_id
                  FROM listings li
                  WHERE listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    return $result;
}

function getAllListings($sort, $deleted, $page, $perPage){
    include ("../../../connection.php");

    $statement = "SELECT l.listing_id AS id, listing_name, price, description, b.borough_name, bt.type_name, address, size
                  FROM listings l INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
                  INNER JOIN boroughs b on l.borough_id = b.borough_id
                  INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id 
                  WHERE lp.date = (SELECT MAX(date) FROM listingprices WHERE listing_id = l.listing_id)
                  AND l.dateDeleted ";
    
    $deletedStub = $deleted  ? "IS NOT NULL " : "IS NULL";
    $statement.=$deletedStub;

    $orderByStub = " ORDER BY";

    if($sort == 0) $orderByStub.= " listing_name DESC";
    if($sort == 1) $orderByStub.= " listing_name ASC";

    if($sort == 2) $orderByStub.= " price DESC";
    if($sort == 3) $orderByStub.= " price ASC";

    if($sort == 4) $orderByStub.= " description DESC";
    if($sort == 5) $orderByStub.= " description ASC";

    if($sort == 6) $orderByStub.= " b.borough_name DESC";
    if($sort == 7) $orderByStub.= " b.borough_name ASC";

    if($sort == 8) $orderByStub.= " bt.type_name DESC";
    if($sort == 9) $orderByStub.= " bt.type_name ASC";

    if($sort == 10) $orderByStub.= " address DESC";
    if($sort == 11) $orderByStub.= " address ASC";

    if($sort == 12) $orderByStub.= " size DESC";
    if($sort == 13) $orderByStub.= " size ASC";

    $statement.=$orderByStub;


    $numberToSkip = ($page - 1) * $perPage;
    
    $statement .= 
    "
     LIMIT :numberToSkip,:perPage
    ";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("numberToSkip", $numberToSkip, PDO::PARAM_INT);
    $prepSt->bindParam("perPage", $perPage, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetchAll();

    return $result;
}

function getDetailedListing($listing_id, $user_id){
    include ("../../../connection.php");

    if($user_id != 0){
        $statement = "SELECT l.listing_id AS id, listing_name, b.borough_name AS borough, b.borough_id AS borough_id, bt.building_type_id AS type_id, bt.type_name AS Type, price, description, address, size,
        (
        SELECT COUNT(*) AS list FROM favorites WHERE user_id = :user_id AND listing_id = l.listing_id
        ) AS favorite, IF(l.dateDeleted IS NULL, true, false) AS active
        FROM listings l 
        INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
        INNER JOIN boroughs b on l.borough_id = b.borough_id
        INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id
        LEFT JOIN favorites f ON l.listing_id = f.listing_id
        ";
    }
    else{
        $statement = "SELECT l.listing_id AS id, listing_name, b.borough_name AS borough, b.borough_id AS borough_id, bt.building_type_id AS type_id, bt.type_name AS Type, price, description, address, size, 0 AS favorite,
        IF(l.dateDeleted IS NULL, true, false) AS active
        FROM listings l 
        INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
        INNER JOIN boroughs b on l.borough_id = b.borough_id
        INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id
        ";
    }

    $statement .= 
    "WHERE lp.date = (SELECT MAX(date) FROM listingprices WHERE listing_id = l.listing_id)
    AND l.listing_id = :listing_id";

    $prepSt = $conn->prepare($statement);

    if($user_id != 0){
        $prepSt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    }

    $prepSt->bindParam(":listing_id", $listing_id, PDO::PARAM_INT);

    $prepSt->execute();

    $result = $prepSt->fetch();

    return $result;
}

function getListingsForFilter($listingTitleFilter, $listingBuildingTypeFilter, $listingBoroughFilter, $user_id, $userFavoriteFilter, $sortType, $page, $perPage){
    include ("../../../connection.php");

    if($user_id != 0){
        $statement = "SELECT DISTINCT l.listing_id AS id, listing_name, b.borough_name AS borough, bt.type_name AS Type, price, description, address, size,
        (
        SELECT COUNT(*) AS list FROM favorites WHERE user_id = :user_id AND listing_id = l.listing_id
        ) AS favorite
        FROM listings l 
        INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
        INNER JOIN boroughs b on l.borough_id = b.borough_id
        INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id
        LEFT JOIN favorites f ON l.listing_id = f.listing_id
        ";
    }
    else{
        $statement = "SELECT DISTINCT l.listing_id AS id, listing_name, b.borough_name AS borough, bt.type_name AS Type, price, description, address, size, 0 AS favorite
        FROM listings l 
        INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
        INNER JOIN boroughs b on l.borough_id = b.borough_id
        INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id
        ";
    }

    $statement .= 
    "WHERE lp.date = (SELECT MAX(date) FROM listingprices WHERE listing_id = l.listing_id)
    AND l.dateDeleted IS NULL ";

    $titleFilter = false;
    $buildingTypeFilter = false;
    $boroughFilter = false;

    if($listingTitleFilter != ""){
        $titleFilter = true;
        $statement .= " AND l.listing_name LIKE :listingTitleFilter"; 
    }

    if(count($listingBuildingTypeFilter) > 0){
        $buildingTypeFilter = true;
        $counter = 0;
        $placeholders = "";
        for($i = 0; $i < count($listingBuildingTypeFilter) - 1; $i++){
            $tag = ":lbuildingtype".$counter++;
            $placeholders .= $tag.", ";
        }
        $tag = ":lbuildingtype".$counter++;
        $placeholders .= $tag;
        $statement .= " AND l.building_type_id IN ($placeholders)";
    }

    if(count($listingBoroughFilter) > 0){
        $boroughFilter = true;
        $counter = 0;
        $placeholders = "";
        for($i = 0; $i < count($listingBoroughFilter) - 1; $i++){
            $tag =":lborough".$counter++;
            $placeholders .= $tag.", ";
        }
        $tag =":lborough".$counter++;
        $placeholders .= $tag;
        $statement .= " AND l.borough_id IN ($placeholders)";
    }

    if($userFavoriteFilter && $user_id != 0){
        $statement .= " AND f.user_id = :user_id";
    }

    $sort = "";

    $sortTypes = array(0 => "l.listing_id DESC", 1 => "price ASC", 2 => "price DESC", 3 => "size ASC", 4 => "size DESC");

    $statement .= " ORDER BY ".$sortTypes[$sortType];

    $numberToSkip = ($page - 1) * $perPage;

    $statement .= 
    "
     LIMIT :numberToSkip,:perPage
    ";

    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("numberToSkip", $numberToSkip, PDO::PARAM_INT);
    $prepSt->bindParam("perPage", $perPage, PDO::PARAM_INT);

    if($titleFilter){
        $listingTitleFilter = "%".$listingTitleFilter."%";
        $prepSt->bindParam(":listingTitleFilter", $listingTitleFilter);
    }

    if($buildingTypeFilter){
        for($i = 0; $i < count($listingBuildingTypeFilter); $i++){
            $tag = ':lbuildingtype'.$i;
            $prepSt->bindValue($tag, $listingBuildingTypeFilter[$i], PDO::PARAM_INT);
        }
    }

    if($boroughFilter){
        for($i = 0; $i < count($listingBoroughFilter); $i++){
            $tag = ':lborough'.$i;
            $prepSt->bindValue($tag, $listingBoroughFilter[$i], PDO::PARAM_INT);
        }
    }
    
    if($user_id != 0){
        $prepSt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    }

    $prepSt->execute();
    $result = $prepSt->fetchAll();
    return $result;
}

function getNumOfListingsForFilter($listingTitleFilter, $listingBuildingTypeFilter, $listingBoroughFilter, $user_id, $userFavoriteFilter, $sortType){
    include ("../../../connection.php");

    $statement = "SELECT COUNT(DISTINCT l.listing_id) AS num
    FROM listings l 
    INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
    INNER JOIN boroughs b on l.borough_id = b.borough_id
    INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id
    LEFT JOIN favorites f ON l.listing_id = f.listing_id
    WHERE lp.date = (SELECT MAX(date) FROM listingprices WHERE listing_id = l.listing_id)
    AND l.dateDeleted IS NULL 
    ";

    $titleFilter = false;
    $buildingTypeFilter = false;
    $boroughFilter = false;

    if($listingTitleFilter != ""){
        $titleFilter = true;
        $statement .= " AND l.listing_name LIKE :listingTitleFilter"; 
    }

    if(count($listingBuildingTypeFilter) > 0){
        $buildingTypeFilter = true;
        $counter = 0;
        $placeholders = "";
        for($i = 0; $i < count($listingBuildingTypeFilter) - 1; $i++){
            $tag = ":lbuildingtype".$counter++;
            $placeholders .= $tag.", ";
        }
        $tag = ":lbuildingtype".$counter++;
        $placeholders .= $tag;
        $statement .= " AND l.building_type_id IN ($placeholders)";
    }

    if(count($listingBoroughFilter) > 0){
        $boroughFilter = true;
        $counter = 0;
        $placeholders = "";
        for($i = 0; $i < count($listingBoroughFilter) - 1; $i++){
            $tag =":lborough".$counter++;
            $placeholders .= $tag.", ";
        }
        $tag =":lborough".$counter++;
        $placeholders .= $tag;
        $statement .= " AND l.borough_id IN ($placeholders)";
    }

    if($userFavoriteFilter && $user_id != 0){
        $statement .= " AND f.user_id = :user_id";
    }

    $prepSt = $conn->prepare($statement);

    if($titleFilter){
        $listingTitleFilter = "%".$listingTitleFilter."%";
        $prepSt->bindParam(":listingTitleFilter", $listingTitleFilter);
    }

    if($buildingTypeFilter){
        for($i = 0; $i < count($listingBuildingTypeFilter); $i++){
            $tag = ':lbuildingtype'.$i;
            $prepSt->bindValue($tag, $listingBuildingTypeFilter[$i], PDO::PARAM_INT);
        }
    }

    if($boroughFilter){
        for($i = 0; $i < count($listingBoroughFilter); $i++){
            $tag = ':lborough'.$i;
            $prepSt->bindValue($tag, $listingBoroughFilter[$i], PDO::PARAM_INT);
        }
    }
    
    if($userFavoriteFilter && $user_id != 0){
        $prepSt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    }

    $prepSt->execute();
    $result = $prepSt->fetch();
    return $result["num"];
}

function getPriceOfListing($listing_id){
    include ("../../../connection.php");

    $statement = "SELECT price FROM listingprices WHERE 
                  listing_id = :listing_id ORDER BY date DESC LIMIT 1";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    return $result;
}

function getAllDeletedListings(){
    include ("../../../connection.php");

    $statement = "SELECT l.listing_id AS id, listing_name, price, description, b.borough_name, bt.type_name, address, size
                  FROM listings l INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
                  INNER JOIN boroughs b on l.borough_id = b.borough_id
                  INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id 
                  WHERE lp.date = (SELECT MAX(date) FROM listingprices WHERE listing_id = l.listing_id)
                  AND l.dateDeleted IS NOT NULL
                  ORDER BY l.listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $result = $prepSt->fetchAll();

    return $result;
}

function restoreListing($id){
    include ("../../../connection.php");

    $statement = "UPDATE listings SET dateDeleted = NULL
                  WHERE listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $id, PDO::PARAM_INT);

    return $prepSt->execute();
}
?>