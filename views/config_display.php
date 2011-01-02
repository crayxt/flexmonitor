<h1><?php echo $sitename?> - configuration</h1>
<form name="AddNewLicense" action="<?php echo $config['base_url']?>config/add/<?php echo $siteid?>" method="get">
    <input type=submit value="Add new license">
    <a href="<?php echo $config['base_url']?>">Cancel</a>
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
    <?php foreach ($result as $row){?>
        <tr>
            <td><?php echo $row[1]?></td>
            <td><?php echo $row[2]?></td>
            <td><?php echo $row[3]?></td>
            <td><?php echo $row[4]?></td>
            <td><form name="Editlicense" action="<?php echo $config['base_url']?>config/add/<?php echo $siteid?>/<?php echo $row[0]?>" method="get">
                <input type="submit" value="Edit"></form></td>
            <td><form name="Deletelicense" action="<?php echo $config['base_url']?>config/delete/<?php echo $siteid?>/<?php echo $row[0]?>" method="get">
                <input type="submit" value="Delete"></form></td>
        </tr>
   <?php } ?>
   </tbody>
</table>