<?php
function saveUserFavorite($user_id, $listing_id){
    include ("../../../connection.php");

    $statement = "INSERT INTO favorites (user_id, listing_id) VALUES (:user_id, :listing_id)";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);
    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function deleteUserFavorite($user_id, $listing_id){
    include ("../../../connection.php");

    $statement = "DELETE FROM favorites WHERE user_id = :user_id AND listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);
    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);

    $prepSt->execute();

    return $conn->lastInsertId();
}
function checkIfAlreadyFavorite($user_id, $listing_id){
    include ("../../../connection.php");

    $statement = "SELECT user_id FROM favorites WHERE user_id = :user_id AND listing_id = :listing_id";
    $prepSt = $conn->prepare($statement);

    $prepSt->bindParam("user_id", $user_id, PDO::PARAM_INT);
    $prepSt->bindParam("listing_id", $listing_id, PDO::PARAM_INT);

    $prepSt->execute();

    $result = $prepSt->fetch();

    if($result){
        return true;
    }
    return false;
}
?>