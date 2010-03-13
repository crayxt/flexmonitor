<?php
ini_set('max_execution_time', 0);
require_once 'config.php';
require_once 'includes/db.php';

//Import variables
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $dbHost = $_POST['dbhost'];//'localhost';
    $user = $_POST['user'];//'root';
    $pass = $_POST['pass'];//'';
    $dbName = $_POST['dbname'];//'licenses_ann';
    $site = $_POST['site'];//'Annemasse';
}else{
    $dbHost = $_GET['dbhost'];//'localhost';
    $user = $_GET['user'];//'root';
    $pass = $_GET['pass'];//'';
    $dbName = $_GET['dbname'];//'licenses_ann';
    $site = $_GET['site'];//'Annemasse';
}
$con = mysql_connect($dbHost, $user, $pass) or die ("Could not connect to db: " . mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_select_db($dbName, $con) or die ("Could not select db: " . mysql_error());

function importdata($licid,$feature){
    foreach ($feature[1] as $licavailable) {
        FlexmonitorDB::getInstance($db)->insert_available_licenses($licavailable[0],$feature[0],$licavailable[1],$licid);;
    }
    $featureid = FlexmonitorDB::getInstance($db)->get_featureid_by_name($feature[0]);
    foreach ($feature[2] as $licused) {
        FlexmonitorDB::getInstance($db)->insert_used_licenses($featureid,$licused[0],'00:00:00',$licused[1],$licid);;
    }
}


$result = mysql_query("select distinct flmavailable_server as license from licenses_available");
while($recordset = mysql_fetch_array($result)){
    $result2 = mysql_query("select distinct flmavailable_product as feature from licenses_available where flmavailable_server='" . $recordset['license'] . "' and flmavailable_date >= '2009-01-01' and flmavailable_date <= '2009-04-01'");
    while($recordset2 = mysql_fetch_array($result2)){
        $result3 = mysql_query("select flmavailable_date,flmavailable_num_licenses from licenses_available where flmavailable_server='" . $recordset['license'] . "' and flmavailable_product='" . $recordset2['feature'] . "' and flmavailable_date >= '2009-01-01' and flmavailable_date <= '2009-04-01'");
        while($recordset3 = mysql_fetch_array($result3)){
            $licenses_available[] = $recordset3;
        }
        $result4 = mysql_query("select flmusage_date,max(flmusage_users) as users from license_usage where flmusage_server='" . $recordset['license'] . "' and flmusage_product='" . $recordset2['feature'] . "' and flmusage_date >= '2009-01-01' and flmusage_date <= '2009-04-01' group by flmusage_server,flmusage_product,flmusage_date");
        while($recordset4 = mysql_fetch_array($result4)){
            $licenses_usage[]=$recordset4;
        }
        $servers[]=array($recordset2['feature'],$licenses_available,$licenses_usage);
        unset ($licenses_available);
        unset ($licenses_usage);
    }
    $licenses[] = array($recordset['license'],$servers);
    unset ($servers);
}

if ($_SERVER["REQUEST_METHOD"] != "POST"){
?>
<form method="post" action="importdata.php">
    <input type="hidden" name="dbhost" value="<?php echo $dbHost?>">
    <input type="hidden" name="user" value="<?php echo $user?>">
    <input type="hidden" name="pass" value="<?php echo $pass?>">
    <input type="hidden" name="dbname" value="<?php echo $dbName?>">
    <input type="hidden" name="site" value="<?php echo $site?>">
    <input type="submit" value="Import">
</form>
<?php
}
foreach ($licenses as $license) {
    $arLicense = explode("@", $license[0]);
    $licid = FlexmonitorDB::getInstance($db)->get_licenses_by_server_site($arLicense[1],$arLicense[0],$site);
    echo "<li>" . $license[0]. " (" . $licid . ")";
    foreach($license[1] as $feature){
        $featureid = FlexmonitorDB::getInstance($db)->get_featureid_by_name($feature[0]);
        echo "<ul>" . $feature[0] . " (" . $featureid . ")</a></ul>";
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            importdata($licid,$feature);
        }
    }
}   echo "</li>";

?>
