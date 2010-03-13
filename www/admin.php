<?php
include_once("./config.php");
require_once("./includes/DB.php");
include 'tpl/header.tpl.php';

$recordset = FlexmonitorDB::getInstance($db)->get_sites();
?>
<body>
<?php include 'tpl/top.tpl.php'?>
<h1>Sites Management</h1>
<input type="button" value="Create Site" onclick="new Ajax.Updater('editsite','editsite.php',{method:'get'});">
    <div id="editsite"></div>
<table border=1>
    <?php
    While($row=mysql_fetch_array($recordset)){?>
    <tr>
        <td> <?php echo $row[1]?></td>
        <td><input type="button" value="Edit Site" onclick="new Ajax.Updater('editsite','editsite.php',{method: 'get',parameters: {site: <?php echo $row[0]?>}});"><a href="deletesite.php?siteid=<?php echo $row['0']?>">Delete Site</a></td>
        <td><input type="button" value="Archive old flexmonitor" onclick="new Ajax.Updater('editsite','configimport.php',{method: 'get',parameters: {site: <?php echo $row[0]?>}});"></td>
    </tr>
<?php
}
?>
</table>

</body>
</html>