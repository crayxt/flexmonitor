<?php
class reportController extends baseController {

    public function index() {

        /*** load the report template ***/
        $this->registry->template->show('report');
    }
}
?>
