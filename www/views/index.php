<?php
require_once __SITE_PATH . '/tpl/header.tpl.php';
?>
<body>
<?php include __SITE_PATH . '/tpl/top.tpl.php'?>
<h1>Sites under monitoring </h1>
<table border=1>
    <?php
    foreach ($result as $siteid => $sitename) {
    ?>
    <tr>
        <td> <?php echo $sitename?></td>
        <td><a href="licenses.php?site=<?php echo $siteid?>">Configuration</a></td>
        <td><a href="servers.php?site=<?php echo $siteid?>">Real Time</a></td>
        <td><a href="monitor.php?site=<?php echo $siteid?>">Graphs</a></td>
    </tr>
<?php
}
?>
</table>

</body>
</html>
