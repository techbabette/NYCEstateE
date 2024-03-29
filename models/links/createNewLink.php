<?php
session_start();
$requiredLevel = 3;
require("../functions/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

    if
    (
    (!isset($_POST["title"])) 
    || (!isset($_POST["href"]))
    || (!isset($_POST["icon"]))
    || (!isset($_POST["aLevel"]))
    || (!isset($_POST["location"]))
    || (!isset($_POST["main"]))
    || (!isset($_POST["priority"]))
    )
    {
        echoImproperRequest("All fields are required");
    }

$LinkTitle = $_POST["title"];
$LinkHref = $_POST["href"];
$LinkIcon = $_POST["icon"];
$AccessLevelId = $_POST["aLevel"];
$LinkLocation = $_POST["location"];
$main = $_POST["main"];
$priority = $_POST["priority"];

$reTitle = '/^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/';
$reHref = '/^(https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[-a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/=]*))||([a-z]{3,40}\.[a-z]{2,5})$/';
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

if($priority < 1){
    echoUnprocessableEntity("Link priority cannot be lower than 1");
}

if($priority > 99){
    echoUnprocessableEntity("Link priority cannot be higher than 99");
}

require("../functions/navigationLocationFunctions.php");
$acceptableLocations = getAllNavigationLocations();

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
require("../functions/linkFunctions.php");
try{
    $lastInsertedId = createNewLink($LinkTitle, $LinkHref, $AccessLevelId, $LinkLocation, $main, $priority);
    if(!empty($LinkIcon)){
        createNewLinkIcon($lastInsertedId, $LinkIcon);
    }
}
catch(PDOException $e){
    echoUnexpectedError();
}

http_response_code(201);
$result["general"] = "Successfully created new link";
echo json_encode($result);
?>