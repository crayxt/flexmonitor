<?php
class reportController extends baseController {

    public function index() {
        $sites = array();
        $featureid = '';
        $features = array();
        $featurename = '';
        $launch = false;
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $sites = $this->registry->db->get_sites_by_featureid($_POST['id']);
            $featureid = $_POST['id'];
            $featurename = $this->registry->db->get_feature_name_byid($featureid);
            $launch = true;
        }else {
            $features = $this->registry->db->get_features();
        }
        /*** load the report template ***/
        $this->registry->template->launch = $launch;
        $this->registry->template->featureid = $featureid;
        $this->registry->template->featurename = $featurename;
        $this->registry->template->features = $features;
        $this->registry->template->sites = $sites;
        $this->registry->template->show('report');
    }

    public function display($args){
        $featureid = '';
        $licenses = '';
        $featurename = '';
        $sitenames = array();
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $featureid = $_POST['id'];
            $sites = implode(',',$_POST['sites']);
            $tmplic = $this->registry->db->get_licid_by_sites_for_featureid($featureid,$sites);
            foreach($tmplic as $licid){
                $sitenames[$licid] = $this->registry->db->get_sitename_by_licid($licid);
            }
            $licenses = implode(',',$tmplic);
            $featurename = $this->registry->db->get_feature_name_byid($featureid);

        }
        $this->registry->template->featureid = $featureid;
        $this->registry->template->featurename = $featurename;
        $this->registry->template->licenses = $licenses;
        $this->registry->template->sitenames = $sitenames;
        $this->registry->template->show('report_display');
    }
}
?>
