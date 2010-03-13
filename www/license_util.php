<?php
ini_set('max_execution_time', 0);
if ( ! is_readable('./config.php') ) {
    echo("<H2>Error: Configuration file config.php does not exist. Please notify your system administrator.</H2>");
    exit;
} 
    include_once 'config.php';
    require_once 'includes/db.php';
    require_once 'includes/licengine.php';

function add_used_licenses($lmutil_loc,$licid,$product,$site,$server,$port,$type,$log){
    fwrite($log, "************************************************************************************************\n");
    fwrite($log, date('r') . " : " . $site . " , "  . $product. " features, server :" . $port . "@" . $server .", licenseid: " . $licid . "\n");
    fwrite($log, "************************************************************************************************\n");
    $license_array = LicEngine::getInstance($lmutil_loc)->get_used_licenses($server,$port,$type);
    if ( isset($license_array) && is_array ($license_array) ) {
        foreach($license_array as $license){
            fwrite($log, date('r') . " : feature -> " . $license["feature"] . " , used -> " . $license["licenses_used"] . "\n");
            FlexmonitorDB::getInstance($db)->insert_used_licenses($license['feature'],date('Y-m-d'),date('H:i') . ":00",$license["licenses_used"],$licid);
        }
        unset($license_array);
    }else{
        fwrite($log, date('r') . " : License server unavailable\n");
    }
    fwrite($log, "************************************************************************************************\n");
}

$log = fopen('logs/license_util.log','w');
    //find licenses
    $recordset = FlexmonitorDB::getInstance($db)->get_licenses();
    while($licenses = mysql_fetch_array($recordset)){
        $siteid = FlexmonitorDB::getInstance($db)->get_site_by_licid($licenses['id']);
        $site = FlexmonitorDB::getInstance($db)->get_site_name_by_id($siteid);
        add_used_licenses($lmutil_loc,$licenses['id'],$licenses['name'],$site,$licenses['hostname'],$licenses['port'],$licenses['type'],$log);
    }
    fclose($log);
?>
