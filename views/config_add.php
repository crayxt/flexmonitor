<form name="EditLicense" action="<?php echo $config['base_url']?>config/add/<?php echo $siteid?>" method="post">
    <input type="hidden" name="licid" value="<?php echo $license['id']?>">
    Host Name <br>
    <input type="text" name="hostname" value="<?php echo $license['hostname']?>"><br>
    Port<br>
    <input type="text" name="port" value="<?php echo $license['port']?>"><br>
    License Name <br>
    <input type="text" name="name" value="<?php echo $license['name']?>"><br>
    License Type<br>
    <select name="typeid">
            <?php
            foreach($types as $key=>$type){
                if($type == $license['type']){?>
                    <option selected value="<?php echo $key?>"><?php echo $type?></option>
                <?php }else{?>
                    <option value="<?php echo $key?>"><?php echo $type?></option>;
                <?php }
            }
            ?>
    </select><br>
    <input type="submit" value="Save Changes">
    <a href="<?php echo $config['base_url']?>config/display/<?php echo $siteid?>">Cancel</a>
</form>
