<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
// If user not logged in, die
if(!isset($_SESSION["user"])){
    echoNoPermission();
}
//If user's access level is too low, die
if($_SESSION["user"]["level"] < $requiredLevel){
    echoNoPermission();
}

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
|| (!isset($_POST["main"]))
)
{
    $result["error"] = "All fields are required";
    $result["data"] = var_dump($_POST);
    http_response_code(422);
    echo json_encode($result);
    die();
}

$LinkId = $_POST["linkId"];
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
    http_response_code(422);
    $result["error"] = "Link title does not match format";
    echo json_encode($result);
    die();
}

if(!preg_match($reHref, $LinkHref)){
    http_response_code(422);
    $result["error"] = "Link does not match format";
    echo json_encode($result);
    die();
}

if(!empty($LinkIcon) && !preg_match($reIcon, $LinkIcon)){
    http_response_code(422);
    $result["error"] = "Link icon does not match format";
    echo json_encode($result);
    die();
}

$linkExists = count(getEveryRowWhereParamFromTable("links", "link_id", $LinkId)) > 0;

if(!$linkExists){
    http_response_code(422);
    $result["error"] = "Provided link id does not exist";
    echo json_encode($result);
    die();
}

$acceptableLocations = array("head", "navbar", "footer");

$locationAcceptable = in_array($LinkLocation, $acceptableLocations);

if(!$locationAcceptable){
    http_response_code(422);
    $result["error"] = "Given location not allowed";
    echo json_encode($result);
    die();
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
    http_response_code(422);
    $result["error"] = "Non existant access level id provided";
    echo json_encode($result);
    die();
}

//Success
require("../DataAccess/linkFunctions.php");
try{
    editLink($LinkId, $LinkTitle, $LinkHref, $AccessLevelId, $LinkLocation, $main);
    if(!empty($LinkIcon)){
        removeAllLinkIcons($LinkId);
        createNewLinkIcon($LinkId, $LinkIcon);
    }
    else{
        removeAllLinkIcons($LinkId);
    }
    $result["general"] = "Success";
    echo json_encode($result);
}
catch(PDOException $e){
    http_response_code(500);
    $result["error"] = "Unexpected error occured";
    // $result["error"] = $e;
    echo json_encode($result);
}
?>