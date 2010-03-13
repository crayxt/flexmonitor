<?php
require_once("./config.php");
require_once("./includes/DB.php");
include 'tpl/header.tpl.php';
if(!isset($_GET['site'])){
    $siteunset = true;
}
else{
    $recordset = FlexmonitorDB::getInstance($db)->get_licenses_by_site_id($_GET['site']);
    $sitename = FlexmonitorDB::getInstance($db)->get_site_name_by_id($_GET['site']);
}
?>
<body>
    <?php
    include 'tpl/top.tpl.php';
    if($siteunset){
     print "<h1>No Site selected</h1>";
	 print "Go back to " . $app_title . " <a href='../'>Homepage</a> to configure a Site";
    }
    else{
    print "<h1>" . $sitename . " - configuration</h1>"
    ?>
    <form name="AddNewLicense" action="editlicense.php" method="get">
    <input type="hidden" name="siteid" value="<?php echo $_GET['site'] ?>">
    <input type=submit value="Add new license">
    <a href="index.php">Cancel</a>
    </form>
        <table border="1">
            <thead>
                <tr>
                    <th>Hostname</th>
                    <th>Port</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            while ($row = mysql_fetch_array($recordset)) {
                ?>
                <tr>
                    <td><?php echo $row[1]?></td>
                    <td><?php echo $row[2]?></td>
                    <td><?php echo $row[3]?></td>
                    <td><?php echo $row[4]?></td>
                    <td><form name="Editlicense" action="editlicense.php" method="get">
                        <input type="hidden" name="siteid" value="<?php echo $_GET['site']?>">
                        <input type="hidden" name="licid" value="<?php echo $row[0]?>">
                        <input type="submit" value="Edit"></form></td>
                    <td><form name="Deletelicense" action="deletelicense.php" method="post">
                    <input type="hidden" name="licid" value="<?php echo $row[0]?>">
                    <input type="hidden" name="siteid" value="<?php echo $_GET['site']?>">
                    <input type="submit" value="Delete"></form></td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>

    
    <?php
    }
    ?>
</body>
</html>
