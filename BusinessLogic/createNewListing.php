<?php
session_start();
$requiredLevel = 4;
require("../DataAccess/generalFunctions.php");
//If user not logged in, die
if(!isset($_SESSION["user"])){
    echoNoPermission();
}
//If user's access level is too low, die
if($_SESSION["user"]["level"] < $requiredLevel){
    echoNoPermission();
}

?>