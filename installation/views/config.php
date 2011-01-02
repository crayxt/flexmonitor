<h1>Flexmonitor Installation Step 3.</h1>
<?php
if(!$finished)
{
?>
<p>Indicate the path to monitoring utilities</p>
<form name="install" action="<?php echo $config['base_url']?>config" method="post">
    <fieldset>
        <legend>License utilities</legend>
        FlexLM utility location <input type="text" name="lmutil_loc"><br />
        LMX utility location <input type="text" name="lmxendutil_loc"><br />
        LUM utility location <input type="text" name="lumutil_loc"><br />
    </fieldset>
    <input type="submit" value="Finish">
</form>
<?php
}else{
?>
<p>Congratulations! Your Flexmonitor installation is done.</p>
<p>Click on following link to start using Flexmonitor.</p>
<a href="<?php echo $appurl ?>">Flexmonitor</a>
<?php
}
?>