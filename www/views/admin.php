<h1>Sites Management</h1>
<form action="<?php echo $config['base_url']?>admin/add"><input type="submit" value="Create Site"></form>
<table border=1>
<?php
foreach($result as $siteid=>$name){?>
    <tr>
        <td> <?php echo $name?></td>
        <td><form action="<?php echo $config['base_url']?>admin/add/<?php echo $siteid?>"><input type="submit" value="Edit Site"></form></td>
        <td><a href="<?php echo $config['base_url']?>admin/delete/<?php echo $siteid?>">Delete Site</a></td>
    </tr>
<?php
}
?>
</table>