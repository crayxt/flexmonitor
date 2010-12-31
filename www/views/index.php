<a href="<?php echo $config['base_url']?>report">Reports</a>
<h1>Sites under monitoring </h1>
<table border="1">
    <?php
    foreach ($result as $siteid => $sitename) {
    ?>
    <tr>
        <td> <?php echo $sitename?></td>
        <td><a href="<?php echo $config['base_url']?>config/display/<?php echo $siteid?>">Configuration</a></td>
        <td><a href="<?php echo $config['base_url']?>realtime/display/<?php echo $siteid?>">Real Time</a></td>
        <td><a href="<?php echo $config['base_url']?>monitor/display/<?php echo $siteid?>">Graphs</a></td>
    </tr>
<?php
}
?>
</table>