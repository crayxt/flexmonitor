<?php
require_once 'config.php';
require_once("./includes/DB.php");
FlexmonitorDB::getInstance($db)->delete_site($_GET['siteid']);
header("Location: admin.php" );
?>
