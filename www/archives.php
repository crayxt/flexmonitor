<?php
require_once 'config.php';
require_once 'includes/db.php';
include 'tpl/header.tpl.php';
$site = FlexmonitorDB::getInstance($db)->get_site_name_by_id($_GET['site']);
$dir = dirname(__FILE__) . "\\archives\\" . $site;
?>
<body>
<?php include 'tpl/top.tpl.php'?>
<h1>Archives 2008</h1>
<?php
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if(!is_dir($file)){
                echo "<ul><a href='archives/" . $site . "/" . $file . "'>" . $file . "<a></ul>";
            }
        }
        closedir($dh);
    }
}
?>
