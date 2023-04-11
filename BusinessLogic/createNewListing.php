<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
//If user not logged in, die
// if(!isset($_SESSION["user"])){
//     echoNoPermission();
// }
//If user's access level is too low, die
// if($_SESSION["user"]["level"] < $requiredLevel){
//     echoNoPermission();
// }

$result;

if(!isset($_FILES["listingPhoto"])){
    $result["error"] = "All fields are required";
    http_response_code(422);
    echo json_encode($result);
    die();
}

$target_dir = "../resources/imgs/";
$nameToSave = basename($_FILES["listingPhoto"]["name"]);
$imageFileType = strtolower(pathinfo($nameToSave,PATHINFO_EXTENSION));
$target_file = $target_dir.time().uniqid(rand()).".".$imageFileType;

$check = getimagesize($_FILES["listingPhoto"]["tmp_name"]);
if($check !== false) {
//   echo "File is an image - " . $check["mime"] . ".";
} 
else{
    $result["error"] = "Uploaded file is not an image";
    http_response_code(422);
    echo json_encode($result);
    die();
}

if ($_FILES["listingPhoto"]["size"] > 8000000) {
    $result["error"] = "Uploaded file too large";
    http_response_code(422);
    echo json_encode($result);
    die();
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $result["error"] = "Uploaded file of incorrect type".$imageFileType;
    http_response_code(422);
    echo json_encode($result);
    die();
}

move_uploaded_file($_FILES["listingPhoto"]["tmp_name"], $target_file);
$result["general"] = "Success";
http_response_code(201);
echo json_encode($result)
?>