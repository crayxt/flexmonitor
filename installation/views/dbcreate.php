<h1>Flexmonitor Installation Step 2.</h1>
<?php
if($back){
?>
<p>Database Configuration</p>
<p><span style="color:red;font-weight:bold;"><?php echo $message?></span></p>
<form name="install" action="<?php echo $config['base_url']?>dbcreate" method="post">
    <fieldset>
        <legend>Database Infos</legend>
        Server <input type="text" name="server" value="<?php echo $server?>"><br />
        Username <input type="text" name="user" value="<?php echo $user?>"><br />
        Password <input type="text" name="pass" value="<?php echo $pass?>"><br />
        Db Name <input type="text" name="dbname" value="<?php echo $dbname?>"><br />
        <input type="radio" name="db" value="create" checked>Create DB<br />
        <input type="radio" name="db" value="update">Update Existing DB<br />
        <input type="radio" name="db" value="use">Use existing DB<br />
    </fieldset>
    <input type="submit" value="Next">
</form>
<?php
}
if($dbcreated)
{
?>
<p>Database Creation successful!</p>
<form name="install" action="<?php echo $config['base_url']?>config" method="get">
    <input type="submit" value="Next">
</form>
<?php
}
?>