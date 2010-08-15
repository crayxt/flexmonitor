<?php
class LicEngine {
  // single instance of self shared among all instances

    private $lmutil_loc = "";
    private $lmxendutil_loc = "";
    private $i4blt_loc = "";
    private static $instance = null;

    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance($arg) {
        if (!self::$instance instanceof self) {
            self::$instance = new self($arg);
        }
        return self::$instance;
    }
    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }
    // private constructor
    private function __construct($arg) {
        $this->lmutil_loc = $arg->lmutil['lmutil_loc'];
        $this->lmxendutil_loc = $arg->lmutil['lmxendutil_loc'];
        $this->i4blt_loc = $arg->lmutil['i4blt_loc'];
     }


    private function datediff($date1,$date2)
    {
        $datediff = $date1 - $date2;
        $full_hours = floor($datediff/(60*60));
        $full_minutes = floor(($datediff - $full_hours*60*60)/60);
        $full_seconds = floor($datediff -$full_hours*60*60 - $full_minutes*60);
        return $full_hours . 'h, ' . $full_minutes . '\', ' . $full_seconds . '\'\' ';
    }

    private function execInBackground($cmd)
    {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        }else{
            exec($cmd . " > /dev/null &");
        }
    }

    private  function getfilename()
    {
        $filename = md5(uniqid(rand(), true)) . '.txt';
        return $filename;
    }

    public function getfile($license)
    {
        $filename = $this->getfilename();
        switch ($license['type'])
        {
            case 'FlexLM':
                popen($this->lmutil_loc . " lmcksum -c " . $license['port'] . "@" . $license['hostname'] . " > " . __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.expire.txt','r');
                popen($this->lmutil_loc . " lmstat -A -c " . $license['port'] . "@" . $license['hostname'] . " > " . __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.txt','r');
                break;
            case 'LMX':
                popen($this->lmxendutil_loc . " -licstat -host " . $license['hostname'] . " -port " . $license['port'] . " > " . __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.txt','r');
                break;
            case 'LUM':
                popen($this->i4blt_loc . " -ll -n " . $license['hostname'] . " > " . __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.expire.txt','r');
                popen($this->i4blt_loc . " -s -lc -n " . $license['hostname'] . " > " . __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.txt','r');
                break;
        }
    }

    public function getfiles($licenses)
    {
        foreach($licenses as $license)
        {
            $this->getfile($license);
        }
    }

    private function loadLUMinfo($license)
    {
        $luminfo = array();
        $luminfo['status'] = Array('class'=>'DOWN','status'=>'NA','version'=>'unknown');
        $luminfo['lic_array'] = array();
        $filename = __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.expire.txt';
        if(!file_exists($filename))
        {
            $this->getfile($license);
        }
        $fp = fopen($filename, 'r');
        while ( !feof ($fp) ) {
            $line = fgets ($fp, 1024);
            if ( eregi("Product Name:", $line)) {
                        $licenseName = explode(":", trim($line));
                        $line = fgets ($fp, 1024);
                        $licenseVersion = explode(":",trim($line));
                        $feature = trim($licenseName[1]). '_' . trim($licenseVersion[1]);
                        $feature_array = array();
                        $luminfo['lic_array'][$feature] = array(
                            'num_licenses' => 0,
                            'licenses_used' => 0,
                            'days_expire' => 'permanent',
                            'date_expire' => 'permanent',
                            'feature_array' => $feature_array);
                        $line = fgets ($fp, 1024);
                        $line = fgets ($fp, 1024);
                        $numLicenses = explode(" ",trim($line));
                        $luminfo['lic_array'][$feature]['num_licenses'] = $numLicenses[0];
                        $line = fgets ($fp, 1024);
                        if ( eregi ($license['hostname'], $line) ) {
                            $luminfo['status'] = Array('class'=>'UP','status'=>'OK','version'=>'4.6.8');
                        }
                        $line = fgets ($fp, 1024);
                        $line = fgets ($fp, 1024);
                        $line = fgets ($fp, 1024);
                        $ar = explode('Exp. Date:',$line);
                        $expireDate = trim($ar[1]);
                        $days_to_expiration = ceil ((1 + strtotime($expireDate) - time()) / 86400);
                        if ( $days_to_expiration > 4000 ) {
                            $expireDate = "permanent";
                            $days_to_expiration = "permanent";
                        }
                        if ( $days_to_expiration < 0 ) {
                            $days_to_expiration = 'expired';
                        }
                        $luminfo['lic_array'][$feature]['days_expire'] = $days_to_expiration;
                        $luminfo['lic_array'][$feature]['date_expire'] = $expireDate;
                    }
        }  
        pclose($fp);
        $filename = __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.txt';
        if(!file_exists($filename))
        {
            $this->getfile($license);
        }
        $fp = fopen($filename , 'r');
        while ( !feof ($fp) ) {
            $line = fgets ($fp, 1024);
            if (preg_match('/(Product Name)/',$line)){
                $licenseName = explode(':',$line);
                $line = fgets ($fp, 1024);
                $licenseVersion = explode(':',$line);
                $feature = trim($licenseName[1]) . '_' . trim($licenseVersion[1]);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                preg_match('/(?:\s*)(\S*)(?:\s*)(\d+)/',$line,$out);
                $luminfo['lic_array'][$feature]['licenses_used'] = $out[2];
                /*if($tempArray[1]>0){
                    $i++;
                    $license_array[$i]["feature"] = $feature;
                    $license_array[$i]["num_licenses"] = $tempArray[0];
                    $license_array[$i]["licenses_used"] = $tempArray[1];
                    $line = fgets ($fp, 1024);
                    $line = fgets ($fp, 1024);
                    $line = fgets ($fp, 1024);
                    for($j=0;$j<$license_array[$i]["licenses_used"];$j++){
                        $ar = split(' +', trim(fgets ($fp, 1024)));
                        $userName = $ar[0] . ' ' . $ar[1];
                        $ar = explode('In-Use Since:',fgets ($fp, 1024));
                        $dateStart = 'In-Use Since: ' . trim($ar[1]);
                        $ar = split(' +', trim(fgets ($fp, 1024)));
                        $machine = $ar[0] . ' ' . $ar[1];
                        $users[$i][] = $userName . ' ' . $machine . ' ' . $dateStart;
                        $line = fgets ($fp, 1024);
                        $line = fgets ($fp, 1024);
                        $line = explode(" In-Use Since: ", $users[$j][$k]);
                        $time_checkedout = strtotime ($line[1]);
                    }
                }*/
            }
        }
        pclose($fp);
        return $luminfo;
    }

    private function loadLMXinfo($license)
    {
        $lmxinfo = array();
        $lmxinfo['status'] = Array('class'=>'DOWN','status'=>'NA','version'=>'unknown');
        $lmxinfo['lic_array'] = array();
        $filename = __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.txt';
        if(!file_exists($filename))
        {
            $this->getfile($license);
        }
        $fp = fopen($filename , 'r');
        $i = -1;
        while ( !feof ($fp) ) {
            $line = fgets ($fp, 1024);
            if ( eregi ('Vendor ENVENTIVE:', $line) ) {
                $line = fgets ($fp, 1024);
                $ar=explode(" ", $line);
                $lmxinfo['status'] = Array('class'=>'UP','status'=>'OK','version'=>$ar[2]);
            }
            if (preg_match('/(Feature:) (.*) (v*)/i',$line,$out)){
                $i++;
                $feature = $out[2];
                $feature_array = array();
                $lmxinfo['lic_array'][$feature] = array('num_licenses' => 0,
                    'licenses_used' => 0,
                    'days_expire' => 'permanent',
                    'date_expire' => 'permanent',
                    'feature_array' => $feature_array);
                $line = fgets ($fp, 1024);
                $ar=explode(" ",$line);
                $expiration_date=$ar[5];
                $days_to_expiration = ceil ((1 + strtotime($expiration_date) - time()) / 86400);
                if ( $days_to_expiration > 4000 ) {
                    $expiration_date = "permanent";
                    $days_to_expiration = "permanent";
                }
                if ( $days_to_expiration < 0 ){
                    $days_to_expiration = 'expired';
                }
                $lmxinfo['lic_array'][$feature]['date_expire'] = $expiration_date;
                $lmxinfo['lic_array'][$feature]['days_expire'] = $days_to_expiration;
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $line = fgets ($fp, 1024);
                $ar=explode(" ",$line);
                $lmxinfo['lic_array'][$feature]['num_licenses'] = $ar[2];
                $lmxinfo['lic_array'][$feature]['licenses_used'] = $ar[0];
            }
            if ( preg_match('/(1 license\(s\) used by)/i', $line ) ){
                $users = explode("license(s) used by ", $line);
                $line = fgets ($fp, 1024);
                $usage_info = $users[1] . $line;
                if(!preg_match('/(Borrow expire time)/i',$line))
                {
                    $ar = explode('Checkout time: ',$line);
                    $time_checkout = strtotime ($ar[1]);
                    $duration_checkout = $this->datediff(time(), $time_checkout);
                }else{
                    $time_checkout = 'NA';
                    $duration_checkout = 'NA';
                }
                $lmxinfo['lic_array'][$feature]['feature_array'][] = array(
                    'usage_info' => $usage_info,
                    'time_checkout' => $time_checkout,
                    'duration_checkout' => $duration_checkout
                );
            }
        }
        pclose($fp);
        return $lmxinfo;
    }

    private function loadFlexLMinfo($license)
    {
        $flexlminfo = array();
        $flexlminfo['status'] = Array('class'=>'DOWN','status'=>'NA','version'=>'unknown');
        $flexlminfo['lic_array'] = array();
        $filename = __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.expire.txt';
        if(!file_exists($filename))
        {
            $this->getfile($license);
        }
        $file = fopen($filename , 'r');
        while (!feof ($file)) {
            $line = fgets ($file, 1024);
            if ( preg_match('/INCREMENT .*/', $line, $out) || preg_match('/FEATURE .*/', $line, $out) ) {
                $ar = explode(" ", $out[0]);
                $feature = $ar[1];
                $feature_array = array();
                if(empty($flexlminfo['lic_array'][$feature]))
                {
                    $flexlminfo['lic_array'][$feature] = array('num_licenses' => '0',
                        'licenses_used' => '0',
                        'days_expire' => 'permanent',
                        'date_expire' => 'permanent',
                        'feature_array' => $feature_array);
                }
                $flexlminfo['lic_array'][$feature]['num_licenses'] += $ar[5];
                $expiration_date = $ar[4];
                if ( $expiration_date )  {
                    $expiration_date = strtolower($expiration_date);
                    $expiration_date = str_replace("-9999", "-2036", $expiration_date);
                    $expiration_date = str_replace("-0000", "-2036", $expiration_date);
                    $expiration_date = str_replace("-2099", "-2036", $expiration_date);
                    $expiration_date = str_replace("-2242", "-2036", $expiration_date);
                    $expiration_date = str_replace("-jan-00", "-jan-2036", $expiration_date);
                    $expiration_date = str_replace("-jan-0", "-jan-2036", $expiration_date);
                    $expiration_date = str_replace("-0", "-2036", $expiration_date);
                    $expiration_date = str_replace("permanent", "05-jul-2036", $expiration_date);
                }
                $days_to_expiration = ceil ((1 + strtotime($expiration_date) - time()) / 86400);
                if ( $days_to_expiration > 4000 ) {
                    $expiration_date = 'permanent';
                    $days_to_expiration = 'permanent';
                }
                if ( $days_to_expiration < 0 ){
                    $days_to_expiration = 'expired';
                }
                $flexlminfo['lic_array'][$feature]['days_expire'] = $days_to_expiration;
                $flexlminfo['lic_array'][$feature]['date_expire'] = $expiration_date;

            }
        }
        pclose($file);
        $filename = __SITE_PATH . '/tmp/' . $license['port'] . '@' . $license['hostname'] . '.txt';
        if(!file_exists($filename))
        {
            $this->getfile($license);
        }
        $fp = fopen($filename, 'r');
        while ( !feof ($fp) ) {
            $line = fgets ($fp, 1024);
            if ( preg_match('/: license server UP/', $line) ) {
                $flexlminfo['status'] =  Array('class'=>'UP','status'=>'OK','version'=>eregi_replace(".*license server UP .*v", "v", $line));
            }
            if ( preg_match('/(?:Users of) (?:(.*)(?::)(?:.*))(?:\(Total of) (\d+) (?:.*) (?:Total of) (\d+) /i', $line, $out) && !preg_match('/No such feature exists/', $line) )
            {
                $feature = $out[1];
                $feature_array = array();
                if(empty($flexlminfo['lic_array'][$feature]))
                {
                    $flexlminfo['lic_array'][$feature] = array('num_licenses' => '0',
                        'licenses_used' => '0',
                        'days_expire' => 'permanent',
                        'date_expire' => 'permanent',
                        'feature_array' => $feature_array);
                }
                $flexlminfo['lic_array'][$feature]['licenses_used'] = $out[3];
                if($flexlminfo['lic_array'][$feature]['num_licenses'] == 0)
                {
                    $flexlminfo['lic_array'][$feature]['num_licenses'] = $out[2];
                }
            }
            if ( eregi(", start", $line ) ){
                $users = explode(", start ", $line);
                preg_match("/(.+?) (.+?) (\d+):(\d+)/i", $users[1], $line2);
                # Convert the date and time ie 12/5 9:57 to UNIX time stamp
                $time_checkout = strtotime ($line2[2] . " " . $line2[3] . ":" . $line2[4]);
                $duration_checkout = $this->datediff(time(), $time_checkout);
                $flexlminfo['lic_array'][$feature]['feature_array'][] = array(
                    'usage_info' => $line,
                    'time_checkout' => $time_checkout,
                    'duration_checkout' => $duration_checkout
                );
            }
        }
        pclose($fp);
        return $flexlminfo;
    }

    public function loadlicenseinfo($license)
    {
        $licinfo = array();
        switch($license['type'])
        {
            case 'FlexLM':
                $licinfo = $this->loadFlexLMinfo($license);
                break;
            case 'LMX':
                $licinfo = $this->loadLMXinfo($license);
                break;
            case 'LUM':
                $licinfo = $this->loadLUMinfo($license);
                break;
        }
        $licinfo['license'] = $license;
        return $licinfo;
    }
}
?>
