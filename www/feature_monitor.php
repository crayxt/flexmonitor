<?php
include_once("./config.php");
include_once("./tpl/header.tpl.php");
require_once 'includes/db.php';
$site = FlexmonitorDB::getInstance($db)->get_site_by_licid($_GET['licid']);
?>
<body>
    <?php include 'tpl/top.tpl.php';?>
<h1>Per Feature License Monitoring</h1>
<p>Back to <a href="monitor.php?site=<?php echo $site?>">Feature list</a></p>
<p>Data is taken every <?php echo($collection_interval); ?> minutes. It shows usage for past day, past week, past month and past year.</p>

<?php
$today = mktime (0,0,0,date("m"),date("d"),  date("Y"));
$periods = array("day","week","month","year");

for ( $i = 0 ; $i < sizeof($periods) ; $i++ ) {
	echo('<h2> ' . ucfirst($periods[$i]) . '</h2><p><img src="generate_monitor.php?featureid=' . $_GET['featureid'] . '&amp;mydate=' . date("Y-m-d", $today) . '&amp;period=' . $periods[$i] . '&amp;licid=' . $_GET['licid'] );
	if ( isset($_GET['upper_limit']) ){
		echo('&amp;upper_limit=' .  htmlspecialchars($_GET['upper_limit']) . '">');
	}else{
		echo("\" alt=\"".htmlspecialchars($_GET['feature'])."\">");
	}
	echo("</p>");
}

?>
</body></html>
