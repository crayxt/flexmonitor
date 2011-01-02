<?php
class dbcreateController extends baseController {

    public function index()
    {
        $back = true;
        $dbcreated = false;
        $message = '';
        $server = 'localhost';
        $user = '';
        $pass = '';
        $dbname = '';
        $this->registry->config['base_url'] = $_COOKIE['base_url'];
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            
            setcookie('hostname',$server = $dbinfo['server'] = $_POST['server']);
            setcookie('username',$user = $dbinfo['user'] = $_POST['user']);
            setcookie('password',$pass = $dbinfo['pass'] = $_POST['pass']);
            setcookie('database',$dbname = $dbinfo['dbname'] = $_POST['dbname']);
            
            if($_POST['db']=='create'){
                $dbinfo['type'] = 'create';
            }
            if($_POST['db']=='update'){
                $dbinfo['type'] = 'update';
            }
            if($_POST['db']=='use'){
                $dbinfo['type'] = 'use';
            }

            $dbcreation = new db_creation($dbinfo);
            if($dbcreation->makedb())
            {
               $back = false;
               $dbcreated = true;
            }else{
                $message = 'Db creation failed';
            }
        }
        $this->registry->template->message = $message;
        $this->registry->template->server = $server;
        $this->registry->template->user = $user;
        $this->registry->template->pass = $pass;
        $this->registry->template->dbname = $dbname;
        $this->registry->template->back = $back;
        $this->registry->template->dbcreated = $dbcreated;
        $this->registry->template->show('dbcreate');
    }
}
?>
