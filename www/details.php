<?php
include_once("./config.php");
require_once ("HTML/Table.php");
require_once("includes/db.php");
include 'tpl/header.tpl.php';
require_once 'includes/licengine.php';

if ( isset($_GET['refresh']) && $_GET['refresh'] > 0 && ! $disable_autorefresh){
	echo('<meta http-equiv="refresh" content="' . intval($_GET['refresh']) . '"/>');
}

$site = FlexmonitorDB::getInstance($db)->get_site_by_licid($_GET['licid']);
?>
</head>
<body>
<?php include 'tpl/top.tpl.php'?>
<h1>Flexlm Licenses in Detail</h1>
<p> Back to <a href="servers.php?site=<?php echo $site?>">Server list</a></p>
<?php

#################################################################
# List available features and their expiration dates
#################################################################
if ( $_GET['listing'] == 1 ) {
	echo('<p>This is a list of licenses (features) available on this particular license server. If there are multiple entries under "Expiration dates" it means there are different entries for the same license. If expiration is in red it means expiration is within ' . $lead_time . ' days.</p>');
	$license = mysql_fetch_array(FlexmonitorDB::getInstance($db)->get_license_by_id($_GET['licid']));
    switch($license['type']){
        case 'FlexLM':
            $lmutil = $lmutil_loc;
            break;
        case 'LMX':
            $lmutil = $lmxendutil_loc;
            break;
    }
    LicEngine::getInstance($lmutil)->get_feature_list($license);

} else {
	########################################################
	# Licenses currently being used
	########################################################
	echo ("<p>Following is the list of licenses currently being used. Licenses that are currently not in use are not shown.</p>");

	$license = mysql_fetch_array(FlexmonitorDB::getInstance($db)->get_license_by_id($_GET['licid']));
    switch($license['type']){
        case 'FlexLM':
            $lmutil = $lmutil_loc;
            break;
        case 'LMX':
            $lmutil = $lmxendutil_loc;
            break;
    }
    LicEngine::getInstance($lmutil)->get_feature_usage($license);
}
?>
</body>
</html>
