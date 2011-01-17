<script type="text/javascript">
    function Appear(name){
        if(document.getElementById(name).style.display == ''){
            document.getElementById(name).style.display = 'none';
        }else{
            document.getElementById(name).style.display = '';
        }
    }
</script>
<h1> <?php echo $sitename ?> License monitoring</h1>
<p>Following links will show the license usage for different tools. Data is being collected every 15 minutes.</p>
<p>Features (click on link to show past usage):</p>

<ul>
<?php
foreach($licenses as $license){?>
    <li onclick="Appear('<?php echo $license['licid']?>');"><a href='#'><?php echo $license['product']?></a></li>
    <ul id="<?php echo $license['licid']?>" style="display: none;">
    <?php foreach ($license['features'] as $feature){?>
        <li><a href="<?php echo $config['base_url']?>monitor/graph/<?php echo $license['licid']?>/<?php echo $feature['featureid']?>"><?php echo $feature['featurename']?></a></li>
    <?php }?>
    </ul>
<?php }?>
</ul>
