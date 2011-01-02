<h1><?php echo $sitename?> - License Servers</h1>
<p>To get current usage for an individual server please click on the "Details" link next to the server.</p>
<table border="1">
    <tr>
    <th>License port@server</th>
    <th>Which licenses</th>
    <th>Status</th>
    <th>Available features/license</th>
    <th>Current Usage</th>
    <th>lmgrd version</th>
    </tr>
<?php
    foreach ($licenses as $license) {
    ?>
    <tr>
        <td><?php echo $license['license']['port']."@".$license['license']['hostname'] ?></td>
        <td><?php echo $license['license']['name']?></td>
        <td class="<?php echo $license['status']['class']?>"><?php echo $license['status']['status']?></td>
        <td><a href="<?php echo $config['base_url']?>realtime/expire/<?php echo $siteid?>/<?php echo $license['license']['id']?>">Listing/Expiration dates</a></td>
        <td><a href="<?php echo $config['base_url']?>realtime/details/<?php echo $siteid?>/<?php echo $license['license']['id']?>">Details</a></td>
        <td><?php echo $license['status']['version']?></td>
    </tr>
<?php
    }
?>
</table>
