<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

    if
    (
    (!isset($_POST["title"]) || empty($_POST["title"])) 
    || (!isset($_POST["href"]) || empty($_POST["href"]))
    || (!isset($_POST["icon"]))
    || (!isset($_POST["aLevel"]))
    || (!isset($_POST["location"]) || empty($_POST["location"]))
    || (!isset($_POST["main"])))
    {
        echoUnprocessableEntity("All fields are required");
    }

$LinkTitle = $_POST["title"];
$LinkHref = $_POST["href"];
$LinkIcon = $_POST["icon"];
$AccessLevelId = $_POST["aLevel"];
$LinkLocation = $_POST["location"];
$main = $_POST["main"];

$reTitle = '/^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/';
$reHref = '/^[a-z]{3,40}\.[a-z]{2,5}$/';
$reIcon = '/^[a-z:-]{5,30}$/';

if(!preg_match($reTitle, $LinkTitle)){
    echoUnprocessableEntity("Link title does not match format");
}

if(!preg_match($reHref, $LinkHref)){
    echoUnprocessableEntity("Link does not match format");
}

if(!empty($LinkIcon) && !preg_match($reIcon, $LinkIcon)){
    echoUnprocessableEntity("Link icon does not match format");
}

$acceptableLocations = array("head", "navbar", "footer");

$locationAcceptable = in_array($LinkLocation, $acceptableLocations);

if(!$locationAcceptable){
    echoUnprocessableEntity("Invalid location provided");
}

$acceptableALevelIds = getEveryParamFromTable("accesslevels", "access_level_id");
$idAcceptable = false;
foreach($acceptableALevelIds as $aLevelId){
    if($aLevelId["access_level_id"] == $AccessLevelId){
        $idAcceptable = true;
        break;
    }
}

if(!$idAcceptable){
    echoUnprocessableEntity("Invalid access level provided");
}

//Success
require("../DataAccess/linkFunctions.php");
try{
    $lastInsertedId = createNewLink($LinkTitle, $LinkHref, $AccessLevelId, $LinkLocation, $main);
    if(!empty($LinkIcon)){
        createNewLinkIcon($lastInsertedId, $LinkIcon);
    }
    http_response_code(201);
    $result["general"] = "Success";
    echo json_encode($result);
}
catch(PDOException $e){
    http_response_code(500);
    $result["error"] = "Unexpected error occured";
    // $result["error"] = $e;
    echo json_encode($result);
}
// $result["data"] = gettype($main);
// echo json_encode($result);
?>