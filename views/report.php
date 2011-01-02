<?php
if($launch){
    $url = $config['base_url'] . 'report/display/';
?>
<form name="report" action="<?php echo $url?>" method="post">
    <h1>2. Choose The Sites</h1>
    <h2>Available Sites for <?php echo $featurename?></h2>
<?php
    if(!empty($sites))
    {
        ?>
    <input type="hidden" name="id" value="<?php echo $featureid?>">
    <fieldset>
        <legend>Sites</legend>
        <?php
        foreach($sites as $site)
        {
            ?><input type="checkbox" name="sites[]" value="<?php echo $site['id']?>"><?php echo $site['name']?><br/><?php
        }
        ?>
    </fieldset>
    <input type="submit" value="Run"><a href="<?php echo $config['base_url']?>report">Back</a><br />
</form>
        <?php
    }
}else{
    $url = $config['base_url'] . 'report/';
    ?>
<h1>1. Choose the Feature</h1>
<?php
    if(empty($features))
    {
        ?>
        <p>No Features yet, start monitoring first.</p>
        <p>Back to <a href="<?php echo $config['base_url']?>">Homepage</a></p>
        <?php
    }else{?>
        <form name="report" action="<?php echo $url?>" method="post">
            <select name="id">
            <?php
            foreach ($features as $feature) {
                if($feature['id']==$featureid){
                    ?>
                <option selected value="<?php echo $feature['id']?>"><?php echo $feature['name']?></option>
                <?php
                }else{?>
                <option value="<?php echo $feature['id']?>"><?php echo $feature['name']?></option>
                <?php
                }
            }
    ?>
    </select>
    <input type="submit" value="Next"><br />
    </form>
<?php
    }
}?>
