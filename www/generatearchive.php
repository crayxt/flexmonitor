<?php
ini_set('max_execution_time', 0);
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/jpgraph/jpgraph.php';
require_once 'includes/jpgraph/jpgraph_line.php';
require_once 'includes/jpgraph/jpgraph_date.php';

//Import variables
$dbHost = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'licenses_ann';
$site = 'Annemasse';

$con = mysql_connect($dbHost, $user, $pass) or die ("Could not connect to db: " . mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_select_db($dbName, $con) or die ("Could not select db: " . mysql_error());

//Function to generate graphs with jpgraph
function extractgraph($feature,$license,$path){
    $graph  = new Graph(600, 250,"auto");
    $graph->SetScale( 'datlin');
    $graph->img->SetMargin(50,40 ,30,70);
    $graph->SetMarginColor("white");
    $graph->title->Set($feature[0]);
    $graph->title->SetFont(FF_VERDANA);
    $graph->xaxis->SetTitle("Time");
    $graph->xaxis->SetTitleMargin(30);
    $graph->yaxis->SetTitleMargin(30);
    $graph->xaxis->title->SetFont(FF_VERDANA);
    $graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,8);
    $graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
    $graph->xgrid->Show();
    $graph->yaxis->title->Set("N° of licenses" );
    $graph->yaxis->title->SetFont(FF_VERDANA);
    $graph->yaxis->scale ->SetGrace(10, 0);
    $graph->SetShadow();
    $graph->xaxis->scale->SetDateFormat('d/m');
    $graph->xaxis->SetLabelAngle(90);
    // Some data
    foreach ($feature[2] as $xvalue => $yvalue) {
        $xdata[] = $xvalue;
        $ydata[]  = $yvalue;
    }
    // Create the linear plot
    $lineplot =new LinePlot($ydata,$xdata);
    $lineplot ->SetColor("blue");
    $lineplot->SetFillColor("lightcyan2:1.3@0.4");
    $sline  = new PlotLine (HORIZONTAL,$feature[1], "red",2);

    // Add the plot to the graph
    $graph->Add( $lineplot);
    $graph->Add( $sline);

    // Display the graph
    $graph->Stroke($path . "\\" . $feature[0] . ".png");
}

$path = dirname(__FILE__) . "\\archives\\" . $site;
if(!is_readable($path)){
    mkdir($path);
}
$xsize = 365;
$startdate = mktime(0,0,0,1,1,2008);
$result = mysql_query("select distinct flmavailable_server as license from licenses_available");
while($recordset = mysql_fetch_array($result)){
    $result2 = mysql_query("select distinct flmavailable_product as feature from licenses_available where flmavailable_server='" . $recordset['license'] . "'");
    while($recordset2 = mysql_fetch_array($result2)){
        $result3 = mysql_query("select max(flmavailable_num_licenses) as num_licenses from licenses_available where flmavailable_server = '" . $recordset['license'] . "' and flmavailable_product = '" . $recordset2['feature'] . "'");
        for($i=0;$i<$xsize;$i++){
            $tmptime = mktime(date('H',$startdate),date('i',$startdate),date('s',$startdate),date('m',$startdate),date('d',$startdate)+$i,date('Y',$startdate));
            $licused[$tmptime] = "x";
        }
        $result4 =  mysql_query("select max(flmusage_users) as flmusage_users,flmusage_date from license_usage where flmusage_server = '" . $recordset['license'] . "' and flmusage_product = '" . $recordset2['feature'] . "' and flmusage_date <='2008-12-31' and flmusage_date >='2008-01-01' group by flmusage_date");
        while($used_licenses = mysql_fetch_array($result4)){
            $arDate = explode("-",$used_licenses['flmusage_date']);
            $licdate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
            $licused[$licdate]= $used_licenses['flmusage_users'];
        }
        extractgraph(array($recordset2['feature'],mysql_result($result3, 0),$licused),$recordset['license'],$path);
    }
}
?>


