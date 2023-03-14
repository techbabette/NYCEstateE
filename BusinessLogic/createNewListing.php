<?php
session_start();
require("../DataAccess/generalFunctions.php");
if(!isset($_SESSION["user"])){
    echoNoPermission();
}
?>