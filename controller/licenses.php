<?php
class licensesController extends baseController {

    public function index() {
        $log = fopen(__SITE_PATH . $this->registry->config['logfile'],'w');
        fwrite($log,date('r') . ": Start\r\n");
        fclose($log);
        $recordset = $this->registry->db->get_licenses();
        while($license = mysql_fetch_array($recordset)){
            $lic_array[] = $license;
        }
        foreach($lic_array as $license)
        {
            $this->insert($license);
        }
        $log = fopen(__SITE_PATH . $this->registry->config['logfile'],'a');
        fwrite($log,date('r') . ": End\r\n");
        fclose($log);

    }

    public function insert($args)
    {
        $log = fopen(__SITE_PATH . $this->registry->config['logfile'],'a');
        fwrite($log,date('r') . ":**********************************************\r\n");
        $license = array('id'=>$args[0],
                'hostname'=>$args[1],
                'port'=>$args[2],
                'name'=>$args[3],
                'type'=>$args[4]);
        
        fwrite($log,date('r') . ": " . implode('-',$license) . "\r\n");
        $status = $this->registry->licengine->getfile($license);
        fwrite($log,date('r') . ": License files uploaded with status $status.\r\n");
        if($status==0)
        {
            $licinfo = $this->registry->licengine->loadlicenseinfo($license);
            if($licinfo['status']['class']=='DOWN')
            {
                fwrite($log,date('r') . ": No license info !!!!!!!!!!!!\r\n");
            }else{
                fwrite($log,date('r') . ": License info array built.\r\n");
                fwrite($log,date('r') . ":----------------------------------------------\r\n");
                $features_array = array();
                foreach($licinfo['lic_array'] as $feature=>$lic_array)
                {
                    $ar = array('feature'=>$feature,
                        'licid'=>$licinfo['license']['id'],
                        'num_licenses'=>$lic_array['num_licenses'],
                        'licenses_used'=>$lic_array['licenses_used'],
                        );
                    fwrite($log,date('r') . ": " . implode(':',$ar) . "\r\n");
                    $features_array[] = $ar;
                }
                fwrite($log,date('r') . ":----------------------------------------------\r\n");
            
            $status = $this->registry->db->insert_used_licenses($features_array);
            fwrite($log,date('r') . ": Features inserted with status $status.\r\n");
            }
        }
        fclose($log);
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
        $YMax = 0;
        $YMax = $this->registry->db->get_max_available_licenses($period,$featureid,date('Y-m-d'),$licid);
        // Some data
        $licavail = array();
        $xavaildata = array();
        $yavaildata = array();
        $recordset = $this->registry->db->get_licenses_available($period,$featureid,date('Y-m-d'),$licid);
        while($avail_licenses = mysql_fetch_array($recordset)){
            $licdate = strtotime($avail_licenses['date']);
            $licavail[$licdate]= $avail_licenses['num_licenses'];
        }
        foreach ($licavail as $xvalue => $yvalue) {
            $xavaildata[] = $xvalue;
            $yavaildata[]  = $yvalue;
        }
        if(empty($yavaildata))
        {
            $xavaildata[0] = time();
            $yavaildata[0] = 0;
        }

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
        $graph->Add( $lineplot);
        if($period == 'day')
        {
            $sline  = new PlotLine (HORIZONTAL,$yavaildata[0], "red",2);
            $graph->Add($sline);
        }else{
            $availlineplot =new LinePlot($yavaildata,$xavaildata);
            $availlineplot ->SetColor("red");
            $availlineplot->SetWeight(2);
            $graph->Add( $availlineplot);
        }
        $graph->Stroke();
    }

    private function cleanData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    public function export($args)
    {
        $licenses = $args[0];
        $featureid = $args[1];
        //filename for download
        $filename = $this->registry->db->get_feature_name_byid($featureid) . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $this->registry->db->get_export_data($featureid,$licenses);
        while($row = mysql_fetch_assoc($result))
            {
            if(!$flag) { # display field/column names as first row
                echo implode("\t", array_keys($row)) . "\n";
                $flag = true;
            }
            array_walk($row, array($this,'cleanData'));
            echo implode("\t", array_values($row)) . "\n";
            }
        exit;
    }
}
?>
