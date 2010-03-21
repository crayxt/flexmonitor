<?php
class indexController extends baseController {

public function index() {
    $recordset = $this->registry->db->get_sites();
    While($row=mysql_fetch_array($recordset)){
        $result[$row[0]] = $row[1];
    }
    $this->registry->template->result = $result;

    /*** load the index template ***/
    $this->registry->template->show('index');
}

}
?>