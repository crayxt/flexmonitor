<?php
ini_set('max_execution_time', 0);
if ( ! is_readable('./config.php') ) {
    echo("<H2>Error: Configuration file config.php does not exist. Please
         notify your system administrator.</H2>");
    exit;
}

include_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/licengine.php';

function add_available_licenses($lmutil_loc,$licid,$product,$site,$server,$port,$type,$log){
    $today = date("Y-m-d");
    fwrite($log, "************************************************************************************************\n");
    fwrite($log, date('r') . " : " . $site . " , "  . $product. " features, server :" . $port . "@" . $server .", licenseid: " . $licid . "\n");
    fwrite($log, "------------------------------------------------------------------------------------------------\n");
    $avail_lic = LicEngine::getInstance($lmutil_loc)->get_available_licenses($server,$port,$type);
    if($avail_lic){
        foreach ($avail_lic as $lic_available) {
            fwrite($log, date('r') . " : feature -> " . $lic_available[0] . " , number -> " . $lic_available[1] . "\n");
            FlexmonitorDB::getInstance($db)->insert_available_licenses($today,$lic_available[0],$lic_available[1],$licid);
        }
    }else{
        fwrite($log, date('r') . " : License server unavailable\n");
    }
    fwrite($log, "************************************************************************************************\n");
}

$log = fopen('logs/license_cache.log','w');
$recordset = FlexmonitorDB::getInstance($db)->get_licenses();
while($licenses = mysql_fetch_array($recordset)){
    $siteid = FlexmonitorDB::getInstance($db)->get_site_by_licid($licenses['id']);
    $site = FlexmonitorDB::getInstance($db)->get_site_name_by_id($siteid);
    add_available_licenses($lmutil_loc,$licenses['id'],$licenses['name'],$site,$licenses['hostname'],$licenses['port'],$licenses['type'],$log);
}
fclose($log);
?>