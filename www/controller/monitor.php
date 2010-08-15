<?php
class monitorController extends baseController {

    public function index()
    {
        header('Location: ' . $this->registry->config['base_url']);
    }

    public function display($args)
    {
        $licenses = array();
        $recordset = $this->registry->db->get_licenses_by_site_id($args[0]);
        while ($row = mysql_fetch_array($recordset)) {
            $result = $this->registry->db->get_features_by_licid($row[0]);
            $features = array();
            while($row2 = mysql_fetch_array($result))
            {
                $features[] = array('featureid' => $row2[0],'featurename' => $row2[1]);
            }
            $licenses[] = Array('licid'=>$row[0],
                'hostname'=>$row[1],
                'port'=>$row[2],
                'product'=>$row[3],
                'features'=>$features);
            unset($features);
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
