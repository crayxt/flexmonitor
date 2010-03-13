<?php
include_once("./config.php");
require_once("./includes/DB.php");
include 'tpl/header.tpl.php';

$recordset = FlexmonitorDB::getInstance($db)->get_sites();

?>
<body>
<?php include 'tpl/top.tpl.php'?>
<h1>Sites under monitoring</h1>
<table border=1>
    <?php
    While($row=mysql_fetch_array($recordset)){?>
    <tr>
        <td> <?php echo $row[1]?></td>
        <td><a href="licenses.php?site=<?php echo $row[0]?>">Configuration</a></td>
        <td><a href="servers.php?site=<?php echo $row[0]?>">Servers</a></td>
        <td><a href="monitor.php?site=<?php echo $row[0]?>">Monitor</a></td>
        <td><a href="archives.php?site=<?php echo $row[0]?>">Archives</a></td>
    </tr>
<?php
}
?>
</table>

</body>
</html>