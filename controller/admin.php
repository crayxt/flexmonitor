<?php
class adminController extends baseController {

    public function index() {
        $result = array();
        $recordset = $this->registry->db->get_sites();
        While($row=mysql_fetch_array($recordset)){
            $result[$row[0]] = $row[1];
        }
        $this->registry->template->result = $result;

        /*** load the admin template ***/
        $this->registry->template->show('admin');
    }

    public function add($args)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            if($_POST['siteid'] == ''){
                $this->registry->db->create_site($_POST['site']);
                header('Location: ' . $this->registry->config['base_url'] . 'admin');
            }else{
                $this->registry->db->update_site($_POST['siteid'],$_POST['site']);
                header('Location: ' . $this->registry->config['base_url'] . 'admin');
            }
        }

        if(isset ($args[0])){
            $site = $this->registry->db->get_site_name_by_id($args[0]);
            $this->registry->template->siteid = $args[0];
            $this->registry->template->site = $site;
        }else{
            $this->registry->template->siteid = '';
            $this->registry->template->site = '';
        }
        
        $this->registry->template->show('admin_add');
    }

    public function delete($args)
    {
        $this->registry->db->delete_site($args[0]);
        header('Location: ' . $this->registry->config['base_url'] . 'admin');
    }
}
?>