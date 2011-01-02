<?php
class configController extends baseController {
    public function index()
    {
        $finished = false;
        $appconfig = '';

        $this->registry->config['base_url'] = $_COOKIE['base_url'];
        $url = explode('installation',$_COOKIE['base_url']);
        $appurl = $url[0];
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){

            array_key_exists('hostname',$_COOKIE) ? $hostname = $_COOKIE['hostname'] : $hostname = "";
            array_key_exists('username',$_COOKIE) ? $username = $_COOKIE['username'] : $username = "";
            array_key_exists('password',$_COOKIE) ? $password = $_COOKIE['password'] : $password = "";
            array_key_exists('database',$_COOKIE) ? $database = $_COOKIE['database'] : $database = "";

            $appconfig .= "<?php\r\n";
            $appconfig .= "\$config['base_url'] = '" . $url[0] . "';\r\n";
            $appconfig .= "\$config['app_title'] = 'Flexmonitor';\r\n";
            $appconfig .= "\$config['app_version']  = '3.0';\r\n";
            $appconfig .= "\$config['logfile'] = '/logs/flexmonitor.log';\r\n";
            $appconfig .= "\$lmutil['lmutil_loc'] ='" . $_POST['lmutil_loc'] . "';\r\n";
            $appconfig .= "\$lmutil['lmxendutil_loc'] ='" . $_POST['lmxendutil_loc'] . "';\r\n";
            $appconfig .= "\$lmutil['i4blt_loc'] ='" . $_POST['lumutil_loc'] . "';\r\n";
            $appconfig .= "\$db['hostname'] ='" . $hostname . "';\r\n";
            $appconfig .= "\$db['username'] ='" . $username . "';\r\n";
            $appconfig .= "\$db['password'] ='" . $password . "';\r\n";
            $appconfig .= "\$db['database'] ='" . $database . "';\r\n";
            $appconfig .= "?>";

            $ar = explode('installation',__SITE_PATH);
            $file = fopen($ar[0] . 'conf/config.php','w');
            fwrite($file,$appconfig);
            fclose($file);

            $finished = true;
        }
        $this->registry->template->appconfig = $appconfig;
        $this->registry->template->appurl = $appurl;
        $this->registry->template->finished = $finished;
        $this->registry->template->show('config');
    }

}
?>
