<?php
class indexController extends baseController {

    public function index() {
            $this->registry->template->result = $this->registry->db->get_sites();
            /*** load the index template ***/
            $this->registry->template->show('index');
    }
}
?>