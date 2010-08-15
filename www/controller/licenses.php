<?php
class licensesController extends baseController {

    public function index() {
        header('Location: ' . $this->registry->config['base_url']);
    }

    public function insert()
    {
        //find licenses
        $init = microtime(true);
        $start = $init;
        $recordset = $this->registry->db->get_licenses();
        while($license = mysql_fetch_assoc($recordset)){
            $lic_array[] = $license;
        }
        $now = microtime(true);
        $duration = $now - $init;
        echo 'fetch licenses took: ' . $duration . ' s<br>';
        $init = $now;
        $this->registry->licengine->getfiles($lic_array);
        $now = microtime(true);
        $duration = $now - $init;
        echo 'get files took: ' . $duration . ' s<br>';
        $init = $now;
        foreach($lic_array as $license)
        {
            $licenses[] = $this->registry->licengine->loadlicenseinfo($license);
        }
        $now = microtime(true);
        $duration = $now - $init;
        echo 'build licenses array took: ' . $duration . ' s<br>';
        $init = $now;
        foreach($licenses as $licinfo)
        {
            foreach($licinfo['lic_array'] as $feature=>$lic_array)
            {
                $features_array[] = array('feature'=>$feature,
                    'licid'=>$licinfo['license']['id'],
                    'num_licenses'=>$lic_array['num_licenses'],
                    'licenses_used'=>$lic_array['licenses_used'],
                    );
            }
        }
        $now = microtime(true);
        $duration = $now - $init;
        echo 'create feature_array took : ' . $duration . ' s<br>';
        $init = $now;
        $this->registry->db->insert_used_licenses($features_array);
        $now = microtime(true);
        $duration = $now - $init;
        echo 'insert licenses in Db took: ' . $duration . ' s<br>';
        $init = $now;
        /*$this->generateimages($features_array);
        $now = time();
        $duration = $now - $init;
        echo 'generate images took: ' . $duration . ' s<br>';*/
        $duration = $now - $start;
        echo 'script total duration : ' . $duration . ' s<br>';
    }

    private function generateimages($features_array)
    {
        foreach($features_array as $feature_array)
        {
            $featureid = $this->registry->db->get_featureid_by_name($feature_array['feature']);
            $this->image(array($feature_array['licid'],$featureid,'day'));
            $this->image(array($feature_array['licid'],$featureid,'week'));
            $this->image(array($feature_array['licid'],$featureid,'month'));
            $this->image(array($feature_array['licid'],$featureid,'year'));
        }
    }

    public function image($args)
    {
        require_once __SITE_PATH . '/includes/jpgraph/jpgraph.php';
        require_once __SITE_PATH . '/includes/jpgraph/jpgraph_line.php';
        require_once __SITE_PATH . '/includes/jpgraph/jpgraph_plotline.php';
        require_once __SITE_PATH . '/includes/jpgraph/jpgraph_date.php';
        require_once __SITE_PATH . '/includes/jpgraph/jpgraph_utils.inc.php';
        //variables init
        $featureid = $args[1];
        $licid = $args[0];
        $period = $args[2];
        $feature = $this->registry->db->get_feature_name_byid($featureid);
        $YMax = $this->registry->db->get_max_available_licenses($licid,$featureid);
        // Some data
        $licused = array();
        $xdata = array();
        $ydata = array();
        $recordset = $this->registry->db->get_licenses_usage($period,$featureid,date('Y-m-d'),$licid);
        while($used_licenses = mysql_fetch_array($recordset)){
            $licdate = strtotime($used_licenses['date'] . '' . $used_licenses['time']);
            $licused[$licdate]= $used_licenses['users'];
        }
        foreach ($licused as $xvalue => $yvalue) {
            $xdata[] = $xvalue;
            $ydata[]  = $yvalue;
        }
        if(empty($ydata))
        {
            $xdata[0] = time();
            $ydata[0] = 0;
        }

        // Create the graph. These two calls are always required
        $graph  = new Graph(610, 260);
        $graph->img->SetMargin(60,40 ,30,80);
        $graph->SetMarginColor("white");
        $graph->title->Set($feature);
        $graph->title->SetFont(FF_VERDANA);
        $graph->SetShadow();
        switch($period){
            case 'day':
                $graph->SetScale('datint',0,$YMax+1,0,0);
                $graph->xaxis->scale->SetDateFormat('H:i');
                break;
            case 'week':
                $startdate = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
                $enddate = mktime(0,0,0,date('m'),date('d')-date('w')+7,date('Y'));
                $graph->SetScale('datint',0,$YMax+1,$startdate,$enddate);
                $graph->xaxis->scale->SetDateFormat('D H:i');
                break;
            case 'month':
                $startdate = mktime(0,0,0,date('m'),1,date('Y'));
                $enddate = mktime(0,0,0,date('m'),date('t'),date('Y'));
                $datax = array();
                for($i=0;$i<=date('t');$i++)
                {
                    $datax[] = $startdate + $i*86400;
                }
                list($tickPositions, $minTickPositions) = DateScaleUtils::GetTicks($datax,DSUTILS_WEEK1);
                $graph->SetScale('datint',0,$YMax+1,$startdate,$enddate);
                $graph->xaxis->SetTickPositions($tickPositions,$minTickPositions);
                $graph->xaxis->SetLabelFormatString('D d/m',true);
                break;
            case 'year':
                $startdate = mktime(0,0,0,1,1,date('Y'));
                $enddate = mktime(0,0,0,12,31,date('Y'));
                $datax = array();
                for($i=0;$i<=365;$i++)
                {
                    $datax[] = $startdate + $i*86400;
                }
                list($tickPositions, $minTickPositions) = DateScaleUtils::GetTicks($datax);
                $graph->SetScale('intint',0,$YMax+1,$startdate,$enddate);
                $graph->xaxis->SetTickPositions($tickPositions,$minTickPositions);
                $graph->xaxis->SetLabelFormatString('M',true);
                break;
        }
        $graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
        $graph->xgrid->Show();
        $graph->xaxis->SetTitle("Time");
        $graph->xaxis->SetLabelAngle(45);
        $graph->xaxis->SetTitleMargin(30);
        $graph->xaxis->title->SetFont(FF_VERDANA);
        $graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,8);
        $graph->yaxis->SetTitleMargin(30);
        $graph->yaxis->title->Set("N° of licenses" );
        $graph->yaxis->title->SetFont(FF_VERDANA);

        // Create the linear plot
        $lineplot =new LinePlot($ydata,$xdata);
        $lineplot ->SetColor("blue");
        $lineplot->SetFillColor("lightcyan2:1.3@0.4");
        $sline  = new PlotLine (HORIZONTAL,$YMax, "red",2);
        $graph->Add( $lineplot);
        $graph->Add( $sline);

        // Display the graph
        /*$filename = __SITE_PATH . '/images/graphs/' . $licid . '-' . $featureid . '-' . $period . '.png';
        if (file_exists($filename))
        {
           unlink($filename);
        }
        $graph->Stroke($filename);*/
        $graph->Stroke();
    }
}
?>
