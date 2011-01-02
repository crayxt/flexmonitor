<?php
class monitorController extends baseController {

    public function index()
    {
        header('Location: ' . $this->registry->config['base_url']);
    }

    public function display($args)
    {
        $licenses = array();
        $recordset = $this->registry->db->get_features_by_siteid($args[0]);
        if (mysql_num_rows($recordset) > 0)
        {
            $tmplic = false;
            $lastlic = FALSE;
            $features = array();
            while ($row = mysql_fetch_array($recordset)) {
                $tmplic = $row[0];
                $tmpprod = $row[1];
                if(($tmplic!=$lastlic) && ($lastlic)){
                    $licenses[] = array(
                        'licid'=>$lastlic,
                        'product'=>$lastprod,
                        'features'=>$features,
                    );
                    unset($features);
                }
                $features[] = array('featureid' => $row[2],'featurename' => $row[3]);
                $lastlic = $tmplic;
                $lastprod = $tmpprod;
            }
            $licenses[] = array(
                        'licid'=>$lastlic,
                        'product'=>$lastprod,
                        'features'=>$features,
                );
        }
        $sitename = $this->registry->db->get_site_name_by_id($args[0]);

        $this->registry->template->licenses = $licenses;
        $this->registry->template->siteid = $args[0];
        $this->registry->template->sitename = $sitename;
        $this->registry->template->show('monitor_display');
    }

    public function graph($args)
    {
        $siteid = $this->registry->db->get_site_by_licid($args[0]);
        $featurename = $this->registry->db->get_feature_name_byid($args[1]);
        
        $this->registry->template->siteid = $siteid;
        $this->registry->template->featurename = $featurename;
        $this->registry->template->featureid = $args[1];
        $this->registry->template->licid = $args[0];
        $this->registry->template->show('monitor_graph');
    }

}
?>
