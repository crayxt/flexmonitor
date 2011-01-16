<?php
class realtimeController extends baseController {

    public function index()
    {
        header('Location: ' . $this->registry->config['base_url']);
    }

    public function display($args)
    {
        $licenses = array();
        $tmplic = $this->registry->db->get_licenses_by_site_id($args[0]);
        foreach($tmplic as $license){
            $licinfo = $this->registry->licengine->loadlicenseinfo($license);
            $licenses[] = $licinfo;
        }
        $sitename = $this->registry->db->get_site_name_by_id($args[0]);

        $this->registry->template->licenses = $licenses;
        $this->registry->template->siteid = $args[0];
        $this->registry->template->sitename = $sitename;
        $this->registry->template->show('realtime_display');
    }
    public function details($args)
    {
        $license = $this->registry->db->get_license_by_id($args[1]);
        $this->registry->licengine->getfile($license);
        $licinfo = $this->registry->licengine->loadlicenseinfo($license);
        $this->registry->template->licinfo = $licinfo;
        $this->registry->template->siteid = $args[0];
        $this->registry->template->license = $license;
        $this->registry->template->show('realtime_details');
    }
    public function expire($args)
    {
        $license = $this->registry->db->get_license_by_id($args[1]);
        $licinfo = $this->registry->licengine->loadlicenseinfo($license);
        $this->registry->template->licinfo = $licinfo;
        $this->registry->template->siteid = $args[0];
        $this->registry->template->license = $license;
        $this->registry->template->show('realtime_expire');
    }
}

?>
