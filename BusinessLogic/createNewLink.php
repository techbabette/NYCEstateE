<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
// If user not logged in, die
// if(!isset($_SESSION["user"])){
//     echoNoPermission();
// }
//If user's access level is too low, die
// if($_SESSION["user"]["level"] < $requiredLevel){
//     echoNoPermission();
// }

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params);
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
        $result["error"] = "All fields are required";
        $result["data"] = var_dump($_POST);
        http_response_code(422);
        echo json_encode($result);
        die();
    }

$LinkTitle = $_POST["title"];
$LinkHref = $_POST["href"];
$LinkIcon = $_POST["icon"];
$AccessLevelId = $_POST["aLevel"];
$location = $_POST["location"];
$main = $_POST["main"];

$reTitle = '/^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/';

if(!preg_match($reTitle, $LinkTitle)){
    http_response_code(422);
    $result["error"] = "Link title does not match format";
    echo json_encode($result);
    die();
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
    http_response_code(422);
    $result["error"] = "Non existing access level id provided";
    echo json_encode($result);
    die();
}

$result["data"] = $acceptableALevelIds;
echo json_encode($result);
?>