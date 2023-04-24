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
   (!isset($_POST["linkId"]))
|| (!isset($_POST["title"]) || empty($_POST["title"])) 
|| (!isset($_POST["href"]) || empty($_POST["href"]))
|| (!isset($_POST["icon"]))
|| (!isset($_POST["aLevel"]))
|| (!isset($_POST["location"]) || empty($_POST["location"]))
|| (!isset($_POST["main"])
|| (!isset($_POST["priority"])))
)
{
    echoUnprocessableEntity("All fields are required");
}

$LinkId = $_POST["linkId"];
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

$linkExists = count(getEveryRowWhereParamFromTable("links", "link_id", $LinkId)) > 0;

if(!$linkExists){
    echoUnprocessableEntity("Provided id link does not exist");
}

require("../DataAccess/navigationLocationFunctions.php");
$acceptableLocations = getAllNavigationLocations();

$locationAcceptable = in_array($LinkLocation, $acceptableLocations);

if(!$locationAcceptable){
    echoUnprocessableEntity("Invalid location provided");
}

$acceptableALevelIds = getEveryParamFromTable("accesslevels", "access_level_id");
$aLIdAcceptable = false;
foreach($acceptableALevelIds as $aLevelId){
    if($aLevelId["access_level_id"] == $AccessLevelId){
        $aLIdAcceptable = true;
        break;
    }
}

if(!$aLIdAcceptable){
    echoUnprocessableEntity("Invalid access level provided");
}

//Success
require("../DataAccess/linkFunctions.php");
$currentLinkIcon = "";
try{
    editLink($LinkId, $LinkTitle, $LinkHref, $AccessLevelId, $LinkLocation, $priority, $main);
    if(!empty($LinkIcon)){
        $currentLinkIcon = getLinkIcon($LinkId);
        if($currentLinkIcon == ""){
            createNewLinkIcon($LinkId, $LinkIcon);
        }
        if($currentLinkIcon != $LinkIcon){
            removeAllLinkIcons($LinkId);
            createNewLinkIcon($LinkId, $LinkIcon);
        }
    }
    $result["general"] = "Successfully edited link";
    echo json_encode($result);
}
catch(PDOException $e){
    echoUnexpectedError();
}
?>