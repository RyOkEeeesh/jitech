<?php
require_once './fn.php';
session_start();
if ((isset($_SESSION['nextTry']) && $_SESSION['nextTry'] > time()) || (isset($_SESSION['try']) && $_SESSION['try'] > 0)) {
  location_login();
}
$_SESSION = [];
session_destroy();
location_login();
?>