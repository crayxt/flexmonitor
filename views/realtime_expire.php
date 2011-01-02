<h1>Flexlm Licenses in Detail</h1>
<p> Back to <a href="<?php echo $config['base_url']?>realtime/display/<?php echo $siteid?>">Licenses list</a></p>
<table border="1" cellpadding="1" cellspacing="2">
    <tr>
        <th colspan="4">Server:  <?php echo $license[2]?>@<?php echo $license[1]?>(<?php echo $license[3]?>)</th>
    </tr>
    <tr>
        <th>Feature</th><th>Number licenses</th><th>Days to expiration</th><th>Date of expiration</th>
    </tr>
    <?php
    foreach ($licinfo['lic_array'] as $feature => $feature_array)
    {
        ?>
    <tr>
        <td><?php echo $feature?></td>
        <td><?php echo $feature_array['num_licenses']?></td>
        <td><?php echo $feature_array['days_expire']?></td>
        <td><?php echo $feature_array['date_expire']?></td>
    </tr>
        <?php
    }
    ?>
</table>