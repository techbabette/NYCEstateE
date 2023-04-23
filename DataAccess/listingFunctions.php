<?php
function createNewListing($user, $borough, $building_type, $name, $description, $address, $size){
    include ("../../connection.php");

    $statement = "INSERT INTO listings (user_id, borough_id, building_type_id, listing_name, description, address, size) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $prepSt = $conn->prepare($statement);
    
    $prepSt->bindParam(1, $user);
    $prepSt->bindParam(2, $borough);
    $prepSt->bindParam(3, $building_type);
    $prepSt->bindParam(4, $name);
    $prepSt->bindParam(5, $description);
    $prepSt->bindParam(6, $address);
    $prepSt->bindParam(7, $size);

    $prepSt->execute(); 

    $last_id = $conn->lastInsertId();
    
    return $last_id;
}

function editListing($listing_id, $borough, $building_type, $name, $description, $address, $size){
    include ("../../connection.php");

    $statement = "UPDATE listings SET (borough_id, building_type_id, listing_name, description, address, size)
                  WHERE listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);

    return $prepSt->execute();
}

function saveListingPrice($listing, $price){
    include ("../../connection.php");

    $statement = "INSERT INTO listingprices (listing_id, price) VALUES (?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $listing, PDO::PARAM_INT);
    $prepSt->bindParam(2, $price);

    $result = $prepSt->execute();

    return $result;
}

function saveMainListingPhoto($listing, $path){
    include ("../../connection.php");

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
    include ("../../connection.php");

    $statement = "INSERT INTO listingrooms (listing_id, room_type_id, numberOf) VALUES (?, ?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $listing, PDO::PARAM_INT);
    $prepSt->bindParam(2, $room, PDO::PARAM_INT);
    $prepSt->bindParam(3, $count, PDO::PARAM_INT);

    $result = $prepSt->execute();

    return $result;
}

function updateListingRoomCount($listing_id, $room_type_id, $numberOf){
    include ("../../connection.php");

    $statement = "UPDATE listingrooms SET numberOf :numberOf
                  WHERE listing_id = :listing_id AND room_type_id = :room_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);
    $prepSt->bindParam("room_type_id", $room_type_id, PDO::PARAM_INT);
    $prepSt->bindParam("numberOf", $numberOf, PDO::PARAM_INT);

    return $prepSt->execute();
}

function removeListingRoom($listing_id, $room_type_id){
    include ("../../connection.php");

    $statement = "DELETE FROM listingrooms
                  WHERE listing_id = :listing_id AND room_type_id = :room_type_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);
    $prepSt->bindParam("room_type_id", $room_type_id, PDO::PARAM_INT);

    return $prepSt->execute();
}

function updateMainListingPhoto($listing, $path){
    include ("../../connection.php");

    $statement = "UPDATE listingphotos SET path = ? WHERE listing_id = ? AND main = true";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $path);
    $prepSt->bindParam(2, $listing, PDO::PARAM_INT);

    $result = $prepSt->execute();

    return $result;
}

function getCurrentMainListingPhoto($listing){
    include ("../../connection.php");

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
    include ("../../connection.php");

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
    include ("../../connection.php");

    $statement = "SELECT listing_name, description, (SELECT price FROM listingprices WHERE listing_id = :listing_id ORDER BY date DESC LIMIT 1) as price, size, address, borough_id, building_type_id
                  FROM listings li
                  WHERE listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    return $result;
}

function getAllListings(){
    include ("../../connection.php");

    $statement = "SELECT l.listing_id AS id, listing_name, price, description, address, size
                  FROM listings l INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
                  WHERE lp.date = (SELECT MAX(date) FROM listingprices WHERE listing_id = l.listing_id)
                  AND l.dateDeleted IS NULL";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $result = $prepSt->fetchAll();

    return $result;
}

function getPriceOfListing($listing_id){
    $statement = "SELECT price FROM listingprices WHERE listing_id = :listing_id ORDER BY date DESC LIMIT 1";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $listing, PDO::PARAM_INT);

    $prepSt->execute();
    $result = $prepSt->fetch();

    return $result;
}

function getAllDeletedListings(){
    include ("../../connection.php");

    $statement = "SELECT l.listing_id AS id, listing_name, price, description, address, size
                  FROM listings l INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
                  WHERE lp.date = (SELECT MAX(date) FROM listingprices WHERE listing_id = l.listing_id)
                  AND l.dateDeleted IS NOT NULL";
    $prepSt = $conn->prepare($statement);

    $prepSt->execute();
    $result = $prepSt->fetchAll();

    return $result;
}

function restoreListing($id){
    include ("../../connection.php");

    $statement = "UPDATE listings SET dateDeleted = NULL
                  WHERE listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("listing_id", $id, PDO::PARAM_INT);

    return $prepSt->execute();
}

function getListings(){
    include ("../../connection.php");

    $statement = "SELECT listing_id, listing_name, borough_name, type_name
                  FROM listings l INNER JOIN boroughs b ON l.borough_id = b.borough_id
                  INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
                  INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id
                  INNER JOIN listingphotos lph ON lph.listing_id = l.listing_id";
}
?>