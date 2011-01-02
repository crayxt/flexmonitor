<?php
class configController extends baseController {

    public function index() {
        header('Location: ' . $this->registry->config['base_url']);
    }


    public function display($args)
    {
        if(isset($args[0]))
        {
            $result = array();
            $recordset = $this->registry->db->get_licenses_by_site_id($args[0]);
            While($row=mysql_fetch_array($recordset)){
                $result[] = $row;
            }
            $this->registry->template->result = $result;
            $this->registry->template->sitename = $this->registry->db->get_site_name_by_id($args[0]);
            $this->registry->template->siteid = $args[0];
            $this->registry->template->show('config_display');
        }
        else echo 'missing argument';
    }
    public function add($args)
    {
        if(isset($args[0]))
        {
            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                if($_POST['licid'] == '')
                {
                    $this->registry->db->insert_license($_POST['hostname'],$_POST['port'],$_POST['name'],$args[0],$_POST['typeid']);
                    header('Location: ' . $this->registry->config['base_url']. 'config/display/' . $args[0]);
                }else{
                    $this->registry->db->update_license($_POST['licid'],$_POST['hostname'],$_POST['port'],$_POST['name'],$_POST['typeid']);
                    header('Location: ' . $this->registry->config['base_url']. 'config/display/' . $args[0]);
                }
            }

            $recordset = $this->registry->db->get_types();
                while($row = mysql_fetch_array($recordset)){
                    $types[$row[0]] = $row[1];
                }
            if(isset($args[1]))
            {
                $license = mysql_fetch_array($this->registry->db->get_license_by_id($args[1]));
            }else{
                $license = array("id" => "", "hostname"=>"","port"=>"","name" => "","type" => "");
            }

            $this->registry->template->license = $license;
            $this->registry->template->types = $types;
            $this->registry->template->siteid = $args[0];
            $this->registry->template->show('config_add');
        }
        else echo 'missing argument';
    }

    public function delete($args)
    {
        $this->registry->db->delete_license($args[1]);
        header('Location: ' . $this->registry->config['base_url'] . 'config/display/' . $args[0]);

    }
}
?>