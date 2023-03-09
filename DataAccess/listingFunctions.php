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
?>