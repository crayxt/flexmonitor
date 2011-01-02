<form method="POST" action="<?php echo $config['base_url'] ?>admin/add">
    Site Name<br>
    <input name="siteid" type="hidden" value="<?php echo $siteid?>">
    <input name="site" type="text" value="<?php echo $site?>"><br>
    <input type="submit" value="Save Changes">
    <a href="<?php echo $config['base_url'] ?>admin">Cancel</a>
</form>