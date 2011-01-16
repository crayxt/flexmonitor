<?php
class DB {
  // single instance of self shared among all instances
    private static $instance = null;

    // db connection config vars
    private $user = "";
    private $pass = "";
    private $dbName = "";
    private $dbHost = "";

    private $con = null;

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
    private function __construct($registry) {
            $this->dbHost = $registry->dbinfo['hostname'];
            $this->user = $registry->dbinfo['username'];
            $this->pass = $registry->dbinfo['password'];
            $this->dbName = $registry->dbinfo['database'];
            $this->con = mysql_connect($this->dbHost, $this->user, $this->pass)
            or die ("Could not connect to db: " . mysql_error());
            //SET NAMES sets client, results, and connection character sets
            mysql_query("SET NAMES 'utf8'");
            mysql_select_db($this->dbName, $this->con)
            or die ("Could not select db: " . mysql_error());
    }

    public function get_site_id_by_name ($name) {
        $name = mysql_real_escape_string($name);
        $result = mysql_query("SELECT id FROM sites WHERE name = '"
            . $name . "'");
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }

    public function get_licid_by_sites_for_featureid($featureid,$sites){
        $result = mysql_query("SELECT distinct(licid) FROM licenses_available WHERE licid IN (SELECT id FROM licenses WHERE siteid IN (" . $sites . ")) AND featureid=" . $featureid . ";");
            while($row = mysql_fetch_array($result))
            {
                $tmplic[] = $row['licid'];
            }
        return $tmplic;
    }


    public function get_max_available_licenses($period,$featureid,$date,$licid) {
        $result = mysql_query("select max(num_licenses) from licenses_available where licid = " . $licid . " and featureid = " . $featureid);
        $arDate = explode("-",$date);
        switch($period){
            case 'day':
                $startDate = $date;
                $endDate = $date;
                break;
            case 'week':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $dayofweek = date('w',$tmpDate);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)-$dayofweek,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)+7-$dayofweek,date('Y',$tmpDate)));
                break;
            case 'month':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate)+1,0,date('Y',$tmpDate)));
                break;
            case 'year':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,1,1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,1,0,date('Y',$tmpDate)+1));
                break;
        }
        $result = mysql_query("select sum(maxlic) from (select max(num_licenses) as maxlic from licenses_available where featureid=" . $featureid . " and licid in (" . $licid . ") and date between '" . $startDate . "' and '" . $endDate . "' group by licid) as num_licenses");
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }

    public function get_export_data($featureid,$licenses)
    {
        $export_data = array();
        $result = mysql_query("SELECT lic_usage.date,SUM(maxusers) AS users,SUM(num_licenses) AS licenses FROM (SELECT DATE,MAX(users) AS maxusers,featureid,licid FROM licenses_usage WHERE featureid=" . $featureid . " AND licid IN (" . $licenses . ") GROUP BY licid,DATE ORDER BY licid,DATE ASC) AS lic_usage JOIN licenses_available ON (lic_usage.featureid = licenses_available.featureid AND lic_usage.licid = licenses_available.licid AND lic_usage.date = licenses_available.date)  GROUP BY DATE ORDER BY DATE ASC;");
        while($row = mysql_fetch_assoc($result))
        {
            $export_data[] = $row;
        }
        return $export_data;
    }

    public function get_site_by_licid($licid) {
        $result =  mysql_query("select siteid from licenses where licenses.id =" . $licid);
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }
    public function get_sitename_by_licid($licid) {
        $result =  mysql_query("select sites.name from licenses,sites where licenses.siteid=sites.id and licenses.id =" . $licid);
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }

    public function get_licenses_by_product($name) {
        $name = mysql_real_escape_string($name);
        return mysql_query("select licenses.id,hostname,port,products.name,type,sites.Name from licenses,ports,products,servers,types,sites where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid=products.id and licenses.typeid=types.id and licenses.siteid = sites.id and products.name=" . $name . "'");
    }

    public function get_licenses_by_server_site($server,$port,$site) {
        $server = mysql_real_escape_string($server);
        $port = mysql_real_escape_string($port);
        $site = mysql_real_escape_string($site);
        $result =  mysql_query("select licenses.id from licenses,ports,servers,sites where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.siteid = sites.id and servers.hostname = '" . $server . "' and ports.port = '" . $port . "' and sites.name = '" . $site . "'");
        if (mysql_num_rows($result) > 0)
            return mysql_result($result, 0);
        else
            return null;
    }

    public function get_server_id_by_name ($name) {
        $name = mysql_real_escape_string($name);
        $result = mysql_query("SELECT id FROM servers WHERE hostname = '"
            . $name . "'");
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }
    
    public function get_port_id_by_name ($name) {
        $name = mysql_real_escape_string($name);
        $result = mysql_query("SELECT id FROM ports WHERE port = '"
            . $name . "'");
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }

    public function get_product_id_by_name ($name) {
        $name = mysql_real_escape_string($name);
        $result = mysql_query("SELECT id FROM products WHERE name = '"
            . $name . "'");
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }

    public function get_site_name_by_id ($id) {
    $result = mysql_query("SELECT name FROM sites WHERE id = " . $id);
    if (mysql_num_rows($result) > 0)
    return mysql_result($result, 0);
    else
    return null;
    }


    public function get_licenses_by_site_id($id) {
        $result = array();
            $recordset = mysql_query("select licenses.id,hostname,port,name,type from licenses,servers,ports,products,types where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid = products.id and licenses.typeid = types.id and siteid='" . $id . "' order by name");
            While($row=mysql_fetch_array($recordset)){
                $result[] = $row;
            }
        return $result;
    }

    public function get_licenses() {
        $recordset = mysql_query("select licenses.id,hostname,port,name,type from licenses,servers,ports,products,types where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid = products.id and licenses.typeid = types.id order by hostname asc");
        while($license = mysql_fetch_array($recordset)){
            $lic_array[] = $license;
        }
        return  $lic_array;
    }

    public function get_license_by_id ($id) {
       return mysql_fetch_array(mysql_query("select licenses.id,hostname,port,name,type from licenses,servers,ports,products,types where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid = products.id and licenses.typeid = types.id and licenses.id='" . $id . "'"));
    }
    public function get_sites() {
        $result = array();
        $recordset = mysql_query("select * from sites order by name");
        while($row=mysql_fetch_array($recordset)){
            $result[$row[0]] = $row[1];
        }
        return $result;
    }

    public function get_sites_by_featureid($id)
    {
        $result = mysql_query("SELECT id,name FROM sites WHERE id IN (SELECT siteid FROM licenses WHERE id IN (SELECT DISTINCT(licid) FROM licenses_available WHERE featureid=" . $id . ")) order by name asc;");
        while($row = mysql_fetch_array($result))
        {
            $sites[] = $row;
        }
        return $sites;
    }

    public function get_types() {
        $recordset = mysql_query("select * from types");
                while($row = mysql_fetch_array($recordset)){
                    $types[$row[0]] = $row[1];
                }
        return $types;
    }

    public function get_features_by_licid($id) {
        return mysql_query("select featureid,features.name from license_features join features on features.id=license_features.featureid where licid=" . $id . " order by features.name asc;");
    }

    public function get_features()
    {
        $features = array();
        $result = mysql_query("SELECT id,features.name FROM features ORDER BY features.name asc;");
        while($row = mysql_fetch_array($result))
        {
            $features[] = $row;
        }
        return $features;
    }
    public function get_features_by_siteid($id) {
        $recordset = mysql_query("SELECT distinct(licid),products.name AS product,featureid,features.name AS feature FROM licenses_available JOIN features ON features.id=licenses_available.featureid JOIN licenses ON licenses_available.licid=licenses.id JOIN products ON licenses.productid = products.id WHERE licenses.siteid=" . $id . " ORDER BY products.name,features.name asc;");
        while ($row = mysql_fetch_array($recordset))
        {
            $features[] = $row;
        }
        return $features;
    }

    public function get_featureid_by_name($name) {
        $result = mysql_query("SELECT id FROM features WHERE name = '" . $name . "'");
        if (mysql_num_rows($result) > 0)
            return mysql_result($result, 0);
        else
            return null;
    }

    public function get_feature_name_byid($id) {
        $result = mysql_query("SELECT name FROM features WHERE id = '" . $id . "'");
        if (mysql_num_rows($result) > 0)
            return mysql_result($result, 0);
        else
            return null;
    }

    
    public function get_licenses_available($period,$featureid,$date,$licid) {
        $arDate = explode("-",$date);
        switch($period){
            case 'day':
                $startDate = $date;
                $endDate = $date;
                break;
            case 'week':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $dayofweek = date('w',$tmpDate);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)-$dayofweek,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)+7-$dayofweek,date('Y',$tmpDate)));
                break;
            case 'month':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate)+1,0,date('Y',$tmpDate)));
                break;
            case 'year':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,1,1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,1,0,date('Y',$tmpDate)+1));
                break;
        }
        $recordset = mysql_query("select sum(num_licenses) as num_licenses,date from licenses_available where featureid=" . $featureid . " and licid in (".$licid . ") and date between '".$startDate."' and '" . $endDate . "' group by date order by date asc;");
        $licavail = array();
        while($avail_licenses = mysql_fetch_array($recordset)){
            $licdate = strtotime($avail_licenses['date']);
            $licavail[$licdate]= $avail_licenses['num_licenses'];
        }
        return $licavail;
    }

    public function get_licenses_usage($period,$featureid,$date,$licid) {
        $arDate = explode("-",$date);
        switch($period){
            case 'day':
                $recordset =  mysql_query("select users,date,time from licenses_usage where featureid= " . $featureid . " and licid= " . $licid . " and date='" . $date . "' order by time asc");
                break;
            case 'week':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $dayofweek = date('w',$tmpDate);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)-$dayofweek,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)+7-$dayofweek,date('Y',$tmpDate)));
                $recordset = mysql_query("select users,date,time from licenses_usage where featureid= " . $featureid . " and licid= " . $licid . " and date between '" . $startDate . "' and '" . $endDate . "' order by date,time asc");
                break;
            case 'month':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate)+1,0,date('Y',$tmpDate)));
                $recordset =  mysql_query("SELECT SUM(users) as users ,date,'00:00:00' AS time FROM(SELECT MAX(users) AS users,DATE, licid FROM licenses_usage WHERE featureid=" . $featureid . " AND licid IN (" . $licid . ") AND DATE BETWEEN '" . $startDate . "' AND '" . $endDate . "' GROUP BY licid,DATE) AS users GROUP BY DATE ORDER BY DATE ASC;");
                break;
            case 'year':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,1,1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,1,0,date('Y',$tmpDate)+1));
                $recordset =  mysql_query("SELECT SUM(users) as users,date,'00:00:00' AS time FROM(SELECT MAX(users) AS users,DATE, licid FROM licenses_usage WHERE featureid=" . $featureid . " AND licid IN (" . $licid . ") AND DATE BETWEEN '" . $startDate . "' AND '" . $endDate . "' GROUP BY licid,DATE) AS users GROUP BY DATE ORDER BY DATE ASC;");
                break;
        }
        $licused = array();
        while($used_licenses = mysql_fetch_array($recordset)){
            $licdate = strtotime($used_licenses['date'] . '' . $used_licenses['time']);
            $licused[$licdate]= $used_licenses['users'];
        }
        return $licused;
        
    }

    public function delete_license ($licID){
        return mysql_query("DELETE FROM licenses WHERE id = " . $licID);
    }

    public function delete_site($siteid) {
        $recordset = $this->get_licenses_by_site_id($siteid);
        while($license = mysql_fetch_array($recordset)){
            $this->delete_license($license['id']);
        }
        return mysql_query("DELETE FROM sites WHERE id = " . $siteid);
        
    }
    public function insert_server($server) {
        $server = mysql_real_escape_string($server);
        mysql_query("INSERT INTO servers (hostname) VALUES ('" . $server. "')");
    }

    public function insert_port($port) {
        $port = mysql_real_escape_string($port);
        mysql_query("INSERT INTO ports (port) VALUES ('" . $port. "')");
    }

    public function insert_product($name) {
        $name = mysql_real_escape_string($name);
        mysql_query("INSERT INTO products (name) VALUES ('" . $name. "')");
    }
    public function insert_license($server,$port,$name,$siteid,$typeid){
        $server = mysql_real_escape_string($server);
        $serverid = $this->get_server_id_by_name($server);
        if(!$serverid){
            $this->insert_server($server);
            $serverid = mysql_insert_id();
        }
        $port = mysql_real_escape_string($port);
        $portid = $this->get_port_id_by_name($port);
        if(!$portid){
            $this->insert_port($port);
            $portid = mysql_insert_id();
        }
        $name = mysql_real_escape_string($name);
        $productid = $this->get_product_id_by_name($name);
        if(!$productid){
            $this->insert_product($name);
            $productid = mysql_insert_id();
        }

        $sql = "INSERT INTO licenses (serverid, portid, productid, siteid, typeid)" .
                           " VALUES (" . $serverid . ", " . $portid . ", " . $productid . ", " . $siteid . "," . $typeid . ")";
        return mysql_query($sql);
    }

    public function update_site($siteid,$name) {
        return mysql_query("UPDATE sites SET name = '" . $name . "' where id = " . $siteid);
    }

    public function update_license($licid,$server,$port,$name,$typeid){
        $server = mysql_real_escape_string($server);
        $serverid = $this->get_server_id_by_name($server);
        if(!$serverid){
            mysql_query("INSERT INTO servers (hostname) VALUES ('" . $server. "')");
            $serverid = mysql_insert_id();
        }
        $port = mysql_real_escape_string($port);
        $portid = $this->get_port_id_by_name($port);
        if(!$portid){
            mysql_query("INSERT INTO ports (port) VALUES ('" . $port. "')");
            $portid = mysql_insert_id();
        }
        $name = mysql_real_escape_string($name);
        $productid = $this->get_product_id_by_name($name);
        if(!$productid){
            mysql_query("INSERT INTO products (name) VALUES ('" . $name. "')");
            $productid = mysql_insert_id();
        }
        
        $sql="UPDATE licenses SET serverid='".$serverid."',portid='".$portid."',productid = '" . $productid . "', typeid = " . $typeid . " WHERE id =" . $licid;
        return mysql_query($sql);
    }

    public function insert_used_licenses($features_array)
    {
        if(!empty($features_array))
        {
        $avail_query_string = "INSERT INTO licenses_available (date,featureid,num_licenses,licid) VALUES ";
        $used_query_string = "INSERT INTO licenses_usage (featureid,date,time,users,licid) VALUES ";
        foreach($features_array as $feature_array)
        {
            $feature = mysql_real_escape_string($feature_array['feature']);
            $featureid = $this->get_featureid_by_name($feature);
            if(!$featureid){
                mysql_query("INSERT INTO features (name) VALUES ('" . $feature. "')");
                $featureid = mysql_insert_id();
            }
            $datas_avail[] = " ('" . date('Y-m-d') . "'," . $featureid . "," . $feature_array['num_licenses'] . "," . $feature_array['licid'] . ")";
            $datas_used[] = "(".$featureid.",'".date('Y-m-d')."','".date('H:i:s')."',".$feature_array['licenses_used'].",".$feature_array['licid'] .")";
        }
        $avail_query_string .= implode(",",$datas_avail);
        $used_query_string .= implode(",",$datas_used);
        $avail_query_string .= " ON DUPLICATE KEY UPDATE num_licenses=VALUES(num_licenses)";
        if(mysql_query($avail_query_string))
        {
            if(mysql_query($used_query_string))
            {
                return 0;
            }else return 1;
        }else return 1;
        }else return 1;
        return 0;
    }

    public function create_site ($name){
        $name = mysql_real_escape_string($name);
        mysql_query("INSERT INTO sites (name) VALUES ('" . $name . "')");
    }


    public function format_date_for_sql($date){
        if ($date == "")
            return "NULL";
        else {
            $dateParts = date_parse($date);
            return $dateParts["year"]*10000 + $dateParts["month"]*100 + $dateParts["day"];
        }
    }
  }
?>
