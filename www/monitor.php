<?php
include_once("./config.php");
require_once("./includes/DB.php");
include 'tpl/header.tpl.php';

if(!isset($_GET['site'])){
    $siteunset=true;
}else{
    $sitename = FlexmonitorDB::getInstance($db)->get_site_name_by_id($_GET['site']);
    $recordset = FlexmonitorDB::getInstance($db)->get_licenses_by_site_id($_GET['site']);
    
}
?>
<script>
    function Appear(name){
        if(document.getElementById(name).style.display == ''){
            document.getElementById(name).style.display = 'none';
        }else{
            document.getElementById(name).style.display = '';
        }
    }

</script>
<body>
<?php
include 'tpl/top.tpl.php';
if($siteunset){
	print "<h1>No Site selected</h1>";
	print "Go back to " . $app_title . " <a href='../'>Homepage</a> to configure a Site";
}
else 
{?>
<h1> <?php echo $sitename ?> License monitoring</h1>
<p>Following links will show the license usage for different tools. Data is being collected every <?php echo($collection_interval); ?> minutes.</p>
<p>Features (click on link to show past usage):</p>

<ul>
<?php

#############################################################
# Print out the list of tools we are showing statistics
#############################################################
while($licenses = mysql_fetch_array($recordset)){
    echo "<li onclick=Appear('" . $licenses[0] ."');><a href='#'>$licenses[3]</a></li>";
    echo "<ul id='" . $licenses[0] . "' style='display: none;'>";
    $result = FlexmonitorDB::getInstance($db)->get_features_by_licid($licenses[0]);
    while($features = mysql_fetch_array($result)){
        echo ('<li><a href="feature_monitor.php?featureid=' . $features[0] . '&amp;licid=' . $licenses[0] . '">' . $features[1] . '</a></li>');
    }
    echo "</ul>";
}
?>
</ul>

<?php

}
?>
</body></html>
