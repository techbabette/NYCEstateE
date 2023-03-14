<?php
function createNewListing($user, $borough, $building_type, $name, $description, $address, $size){
    include ("connection.php");

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

    $last_id = $conn->lastInsertedId();
    
    return $last_id;
}

function saveListingPrice($listing, $price){
    include ("connection.php");

    $statement = "INSERT INTO listingprices (listing_id, price) VALUES (?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $listing);
    $prepSt->bindParam(2, $price);
}

function saveMainListingPhoto($listing, $path){
    include ("connection.php");

    $main = true;

    $statement = "INSERT INTO listingphotos (listing_id, path, main) VALUES (?, ?, ?)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $listing, PDO::PARAM_INT);
    $prepSt->bindParam(2, $path);
    $prepSt->bindParam(3, $main);
}

function updateMainListingPhoto($listing, $path){
    include ("connection.php");

    $statement = "UPDATE listingphotos SET path = ? WHERE listing_id = ? AND main = true";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam(1, $path);
    $prepSt->bindParam(2, $listing, PDO::PARAM_INT);
}

function getListings(){
    include ("connection.php");

    $statement = "SELECT listing_id, listing_name, borough_name, type_name
                  FROM listings l INNER JOIN boroughs b ON l.borough_id = b.borough_id
                  INNER JOIN listingprices lp ON l.listing_id = lp.listing_id
                  INNER JOIN buildingtypes bt ON l.building_type_id = bt.building_type_id
                  INNER JOIN listingphotos lph ON lph.listing_id = l.listing_id";
}
?>