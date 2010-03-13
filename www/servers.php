<?php
ini_set('max_execution_time', 0);
include_once 'config.php';
require_once 'includes/DB.php';
require_once 'includes/licengine.php';
include 'tpl/header.tpl.php';
if(!isset($_GET['site'])){
    $siteunset=true;
}
else{
    $recordset = FlexmonitorDB::getInstance($db)->get_licenses_by_site_id($_GET['site']);
    $sitename = FlexmonitorDB::getInstance($db)->get_site_name_by_id($_GET['site']);
}
while ($row = mysql_fetch_array($recordset)) {
    switch($row['type']){
        case 'LMX':
            $lmutil = $lmxendutil_loc;
            break;
        case 'FlexLM':
            $lmutil = $lmutil_loc;
            break;
    }
    $status = LicEngine::getInstance($lmutil)->get_licmanager_status($row['hostname'],$row['port'],$row['type']);
    if($status[0]=="UP"){
        $class="up";
        $detaillink = "<a href=\"details.php?listing=1&amp;licid=" . $row[0] . "\">Listing/Expiration dates</a>";
        $listinglink = "<a href=\"details.php?listing=0&amp;licid=" . $row[0] . "\">Details</a>";
    }else{
        $class = "down";
        $detaillink = "details unavailable";
        $listinglink = "listing unavailable";
    }
 $servers[] = Array("hostname"=>$row[1],"port"=>$row[2],"product"=>$row[3],"status"=>$status[0],"class"=>$class,"listing"=>$listinglink,"details"=>$detaillink,"licmgrver"=>$status[2]);
}
?>
<body>
<?php
include 'tpl/top.tpl.php';
if($siteunset){
print "<h1>No Site selected</h1>";
print "Go back to " . $app_title . " <a href='../'>Homepage</a> to configure a Site";
}
else 
{
   print "<h1>" . $sitename . " - License Servers</h1>";?>
<p>To get current usage for an individual server please click on the "Details" link next to the server.</p>
<table border="1">
    <tr>
    <th>License port@server</th>
    <th>Which licenses</th>
    <th>Status</th>
    <th>Current Usage</th>
    <th>Available features/license</th>
    <th>lmgrd version</th>
    </tr>
<?php
if($servers){
    foreach ($servers as $server) {
    ?>
    <tr>
        <td><?php echo $server['port']."@".$server['hostname'] ?></td>
        <td><?php echo $server['product']?></td>
        <td class="<?php echo $server['class']?>"><?php echo $server['status']?></td>
        <td><?php echo $server['listing']?></td>
        <td><?php echo $server['details']?></td>
        <td><?php echo $server['licmgrver']?></td>
    </tr>
<?php
        }
    }
}?>
</table>
</body></html>
