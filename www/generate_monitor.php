<?php
require_once 'includes/jpgraph/jpgraph.php';
require_once 'includes/jpgraph/jpgraph_line.php';
require_once 'includes/jpgraph/jpgraph_date.php';
require_once 'includes/db.php';
include_once 'config.php';

//variables init
$featureid = $_GET['featureid'];
$licid = $_GET['licid'];
$feature = FlexmonitorDB::getInstance($db)->get_feature_name_byid($featureid);
$period = $_GET['period'];
$arDate = explode("-",$_GET['mydate']);
$date = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);

// Create the graph. These two calls are always required
$graph  = new Graph(600, 250,"auto");
$graph->SetScale( 'datlin');
$graph->img->SetMargin(50,40 ,30,70);
$graph->SetMarginColor("white");
$graph->title->Set($feature);
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


switch($period){
    case 'day':
        $xsize = 1440/$collection_interval;
        $startdate = $date;
        $timeinterval = $collection_interval;
        $dateinterval = 0;
        $graph->xaxis->scale->SetDateFormat('H:i');
        $graph->SetTickDensity(TICKD_NORMAL,TICKD_VERYSPARSE);
        break;
    case 'week':
        $xsize = 7*1440/$collection_interval;
        $startdate = mktime(0,0,0,date('m',$date),date('d',$date)-date('w',$date),date('Y',$date));
        $timeinterval = $collection_interval;
        $dateinterval = 0;
        $graph->xaxis->scale->SetDateFormat('D');
        $graph->SetTickDensity(TICKD_NORMAL,TICKD_VERYSPARSE);
        break;
    case 'month':
        $xsize = date('t',$date);
        $startdate = mktime(0,0,0,date('m',$date),1,date('Y',$date));
        $timeinterval = 0;
        $dateinterval = 1;
        $graph->xaxis->scale->SetDateFormat('d/m');
        $graph->xaxis->SetLabelAngle(90);
        break;
    case 'year':
        $xsize = 365;
        $startdate = mktime(0,0,0,1,1,date('Y',$date));
        $timeinterval = 0;
        $dateinterval = 1;
        $graph->xaxis->scale->SetDateFormat('d/m');
        $graph->xaxis->SetLabelAngle(90);
        break;
}
for($i=0;$i<$xsize;$i++){
            $tmptime = mktime(date('H',$startdate),date('i',$startdate)+$timeinterval*$i,date('s',$startdate),date('m',$startdate),date('d',$startdate)+$dateinterval*$i,date('Y',$startdate));
            $licused[$tmptime] = "x";
}
$YMax = FlexmonitorDB::getInstance($db)->get_max_available_licenses($licid,$featureid);


// Some data
$recordset = FlexmonitorDB::getInstance($db)->get_licenses_usage($period,$featureid,date('Y-m-d',$date),$licid);
while($used_licenses = mysql_fetch_array($recordset)){
    $arDate = explode("-",$used_licenses['date']);
    $arTime = explode(":",$used_licenses['time']);
    $licdate = mktime($arTime[0],$arTime[1],$arTime[2],$arDate[1],$arDate[2],$arDate[0]);
    $licused[$licdate]= $used_licenses['users'];
}
foreach ($licused as $xvalue => $yvalue) {
    $xdata[] = $xvalue;
    $ydata[]  = $yvalue;
}

// Create the linear plot
$lineplot =new LinePlot($ydata,$xdata);
$lineplot ->SetColor("blue");
//$lineplot->SetWeight(2);
$lineplot->SetFillColor("lightcyan2:1.3@0.4");
$sline  = new PlotLine (HORIZONTAL,$YMax, "red",2);

// Add the plot to the graph
$graph->Add( $lineplot);
$graph->Add( $sline);

// Display the graph
$graph->Stroke();
?>
