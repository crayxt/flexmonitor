<?php
require_once 'config.php';
require_once 'includes/db.php';
$site = FlexmonitorDB::getInstance($db)->get_site_name_by_id($_GET['site'])
?>
<form method="GET" action="importdata.php">
    <input type="hidden" name="site" value="<?php echo $site?>"><?php echo $site;?><br>
    DB server<br>
    <input type="text" name="dbhost"><br>
    DB user name<br>
    <input type="text" name="user"><br>
    DB user passwd<br>
    <input type="text" name="pass"><br>
    DB name<br>
    <input type="text" name="dbname"><br>
    <input type="submit" value="Save Changes">
    <a href="admin.php">Cancel</a>
</form>