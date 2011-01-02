<?php
class indexController extends baseController {

    public function index() {
            $url = 'http://' . $_SERVER['SERVER_NAME'];
            if(80 != $_SERVER['SERVER_PORT']){$url .= $_SERVER['SERVER_PORT'];}
            $path = explode('index.php',$_SERVER['REQUEST_URI']);
            $url .= $path[0];
            setcookie('base_url',$url);
            $this->registry->config['base_url'] = $url;
            
            /*** load the index template ***/
            $this->registry->template->show('index');
    }
}
?>