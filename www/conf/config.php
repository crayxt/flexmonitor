<?php
/*
 * Basic config
 */
$config['base_url'] = 'http://localhost/flexmonitor/';
$config['app_title'] = 'Flexmonitor Dev';
$config['app_version']  = '2.0';

/*
 * License manager tools location
 */
$lmutil['lmutil_loc'] = 'C:\\Windows\\system32\\lmutil.exe';
$lmutil['lmxendutil_loc'] = 'C:\\Windows\\system32\\lmxendutil.exe';
$lmutil['i4blt_loc'] = 'C:\\IFOR\\WIN\\BIN\\i4blt.exe';

/*
 * Database configuration
 */
$db['hostname'] = 'vss-cre-s0005';
$db['username'] = 'license';
$db['password'] = 'license';
$db['database'] = 'licenses';

$LUM_timeout=4;
$lead_time=10;
$disable_autorefresh=0;
$collection_interval=15;
$colors="#ffffdd,#ff9966, #ffffaa,#ccccff,#cccccc,#ffcc66,#99ff99,#eeeeee,#66ffff,#ccffff, #ffff66, #ffccff,#ff66ff, yellow,lightgreen,lightblue";
$smallgraph="300,200";
$largegraph="600,300";
$legendpoints="";
$xlegendpoints = 12;
?>
