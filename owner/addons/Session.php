<?php 

session_start();
require_once("./db/config.php");
require_once("./utils/helpers.php");
require_once("./store/parcel.store.php");
require_once("./store/timeline.store.php");
require_once("./store/user.store.php");

if(!isset($_SESSION['ADMIN_SESSION'])) redirect('./');

$adminDetails = json_decode($_SESSION['ADMIN_SESSION'], true);
$ADMIN_EMAIL = $adminDetails['email'];
$ADMIN_ID = $adminDetails['id'];
$ADMIN_USERNAME = $adminDetails['username'];