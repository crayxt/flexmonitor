<?php
require_once 'config.php';
require_once("./includes/DB.php");
FlexmonitorDB::getInstance($db)->delete_license($_POST['licid']);
header("Location: licenses.php?site=" . $_POST['siteid'] );
?>