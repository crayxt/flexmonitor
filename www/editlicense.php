<?php
require_once 'config.php';
require_once 'Includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
if($_POST['licid']=="")
{
    FlexmonitorDB::getInstance($db)->insert_license($_POST['hostname'],$_POST['port'],$_POST['name'],$_POST['siteid'],$_POST['typeid']);
    header("Location: licenses.php?site=" . $_POST['siteid']);
}else{
if($_POST['licid']!=""){
    FlexmonitorDB::getInstance($db)->update_license($_POST['licid'],$_POST['hostname'],$_POST['port'],$_POST['name'],$_POST['typeid']);
    header("Location: licenses.php?site=" . $_POST['siteid']);
}
}
}
?>
<html>
<body>
<?php
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        $license = array("id" => $_POST["licid"],"hostname"=>$_POST["hostname"],"port"=>$_POST["port"],"name" => $_POST["name"],"type" => $_POST['type']);
        else
        if (array_key_exists("licid", $_GET))
        $license = mysql_fetch_array(FlexmonitorDB::getInstance($db)->get_license_by_id($_GET["licid"]));
        else
        $license = array("id" => "", "hostname"=>"","port"=>"","name" => "","type" => "");
?>
<form name="EditLicense" action="editlicense.php" method="post">
    <input type="hidden" name="licid" value="<?php echo $license['id']?>">
    Host Name <br>
    <input type="text" name="hostname" value="<?php echo $license['hostname']?>"><br>
    Port<br>
    <input type="text" name="port" value="<?php echo $license['port']?>"><br>
    License Name <br>
    <input type="text" name="name" value="<?php echo $license['name']?>"><br>
    License Type<br>
    <select name="typeid">
            <?php
            $result = FlexmonitorDB::getInstance($db)->get_types();
            while($type = mysql_fetch_array($result)){
                if($type[1]==$license['type']){
                    echo "<option selected value=".$type[0].">".$type[1]."</option>";
                }else{
                    echo "<option value=".$type[0].">".$type[1]."</option>";
                }
            }
            ?>
    </select><br>
    <input type="hidden" name="siteid" value="<?php echo $_GET['siteid']?>">
    <input type="submit" value="Save Changes">
    <a href="licenses.php?site=<?php echo $_GET['siteid']?>">Cancel</a>
</form>
</body>
</html>
