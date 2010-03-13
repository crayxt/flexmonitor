<?php
require_once 'config.php';
require_once 'includes/db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST['siteid']==""){
        FlexmonitorDB::getInstance($db)->create_site($_POST['site']);
        header("Location: admin.php");
    }else{
        if($_POST['site']==""){
            $siteisempty=true;
        }else{
            FlexmonitorDB::getInstance($db)->update_site($_POST['siteid'],$_POST['site']);
            header("Location: admin.php");
        }
    }
    

}?>
<?php
if(isset ($_GET['site'])){
        $site = FlexmonitorDB::getInstance($db)->get_site_name_by_id($_GET['site']);
}
if(!$siteisempty){
?>
<form method="POST" action="editsite.php">
    Site Name<br>
    <input name="siteid" type="hidden" value="<?php echo $_GET['site']?>">
    <input name="site" type="text" value="<?php echo $site?>"><br>
    <input type="submit" value="Save Changes">
    <a href="admin.php">Cancel</a>
</form>
<?php
}else{
?>
<form method="POST" action="editsite.php">
    <span style="color: red;">Please fill the Site Name</span><br>
    Site Name<br>
    <input name="siteid" type="hidden" value="<?php echo $_POST['siteid']?>">
    <input name="site" type="text" value="<?php echo $_POST['site']?>"><br>
    <input type="submit" value="Save Changes">
    <a href="admin.php">Cancel</a>
</form>
<?php
}
?>