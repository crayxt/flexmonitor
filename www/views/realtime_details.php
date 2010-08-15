<h1><?php echo $license['name']?> License in Detail</h1>
<p> Back to <a href="<?php echo $config['base_url']?>realtime/display/<?php echo $siteid?>">Server list</a></p>
<p>Following is the list of licenses currently being used.</p>
<table border="1">
    <tr>
        <th colspan="4">Server:<?php echo $license['hostname']?>(<?php echo $license['name']?>)</th>
    </tr>
    <tr>
        <th>Feature</th><th># cur. avail</th><th>Details</th><th>Time checked out</th>
    </tr>
    <?php foreach($licinfo['lic_array'] as $feature => $usage_feature)
    {?>
        <tr>
            <td><?php echo $feature?></td>
            <td><?php echo $usage_feature['num_licenses'] - $usage_feature['licenses_used']?></td>
            <td>Total of <?php echo $usage_feature['num_licenses']?> licenses, <?php echo $usage_feature['licenses_used']?> currently in use,<b> <?php echo $usage_feature['num_licenses'] - $usage_feature['licenses_used']?> available</b></td>
            <td>&nbsp;</td>
        </tr>
        <?php foreach($usage_feature['feature_array'] as $feature_array )
        {?>
            <tr>
                <td></td>
                <td></td>
                <td><?php echo $feature_array['usage_info']?></td>
                <td><?php echo $feature_array['duration_checkout']?></td>
            </tr>
        <?php
        }
    }
    ?>
</table>