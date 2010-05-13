<?php
ini_set('max_execution_time', 0);
class FlexmonitorDB {
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
    private function __construct($db) {
        $this->dbHost = $db['hostname'];
        $this->user = $db['username'];
        $this->pass = $db['password'];
        $this->dbName = $db['database'];
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

    public function get_max_available_licenses($licid,$featureid) {
        $result = mysql_query("select max(num_licenses) from licenses_available where licid = " . $licid . " and featureid = " . $featureid);
        if (mysql_num_rows($result) > 0) return mysql_result($result, 0);
        else return null;
    }

    public function get_site_by_licid($licid) {
        $result =  mysql_query("select siteid from licenses where id =" . $licid);
        if (mysql_num_rows($result) > 0)
        return mysql_result($result, 0);
        else
        return null;
    }

    public function get_licenses_by_product($name) {
        $name = mysql_real_escape_string($name);
        return mysql_query("select licenses.id,hostname,port,products.name,type,sites.Name from licenses,ports,products,servers,types,sites where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid=products.id and licenses.typeid=types.id and licenses.siteid = sites.id and products.name= '" . $name . "'");
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
        return  mysql_query("select licenses.id,hostname,port,name,type from licenses,servers,ports,products,types where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid = products.id and licenses.typeid = types.id and siteid='" . $id . "'");
    }

    public function get_licenses() {
        return  mysql_query("select licenses.id,hostname,port,name,type from licenses,servers,ports,products,types where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid = products.id and licenses.typeid = types.id");
    }

    public function get_license_by_id ($id) {
       return mysql_query("select licenses.id,hostname,port,name,type from licenses,servers,ports,products,types where licenses.serverid=servers.id and licenses.portid=ports.id and licenses.productid = products.id and licenses.typeid = types.id and licenses.id='" . $id . "'");
    }
    public function get_sites() {
        return mysql_query("select * from sites order by name");
    }

    public function get_types() {
        return mysql_query("select * from types");
    }

    public function get_features_by_licid($id) {
        return mysql_query("select distinct(features.id),features.name,num_licenses from licenses_available,features where features.id=licenses_available.featureid and licid=".$id);
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

    
    public function get_licenses_available($featureid,$date,$licid) {
        $result = mysql_query("select num_licenses from licenses_available where featureid=" . $featureid . " and date='".$date."' and licid=".$licid);
        if (mysql_num_rows($result) > 0)

            return mysql_result($result, 0);
        else
            return null;
    }

    public function get_licenses_usage($period,$featureid,$date,$licid) {
        $arDate = explode("-",$date);
        switch($period){
            case 'day':
                $tmpDate = date('Y-m-d',mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]));
                return mysql_query("select users,date,time from licenses_usage where featureid= " . $featureid . " and licid= " . $licid . " and date <='" . $date . "' and date >='" . $tmpDate . "'");
                break;
            case 'week':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $dayofweek = date('w',$tmpDate);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)-$dayofweek,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)+7-$dayofweek,date('Y',$tmpDate)));
                return mysql_query("select users,date,time from licenses_usage where featureid= " . $featureid . " and licid= " . $licid . " and date <='" . $endDate . "' and date >='" . $startDate . "'");
                break;
            case 'month':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate),1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,date('m',$tmpDate)+1,0,date('Y',$tmpDate)));
                return mysql_query("select max(users) as users,date,'00:00:00' as time from licenses_usage where featureid= " . $featureid . " and licid= " . $licid . " and date <='" . $endDate . "' and date >='" . $startDate . "' group by date");
                break;
            case 'year':
                $tmpDate = mktime(0,0,0,$arDate[1],$arDate[2],$arDate[0]);
                $startDate = date('Y-m-d',mktime(0,0,0,1,1,date('Y',$tmpDate)));
                $endDate = date('Y-m-d',mktime(0,0,0,1,0,date('Y',$tmpDate)+1));
                return mysql_query("select max(users) as users,date,time from licenses_usage where featureid= " . $featureid . " and licid= " . $licid . " and date <='" . $endDate . "' and date >='" . $startDate . "' group by date");
                break;
        }
        
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

    public function insert_available_licenses($date,$feature,$lic_number,$licid) {
        $feature = mysql_real_escape_string($feature);
        $featureid = $this->get_featureid_by_name($feature);
        if(!$featureid){
            mysql_query("INSERT INTO features (name) VALUES ('" . $feature. "')");
            $featureid = mysql_insert_id();
        }
        mysql_query("insert into licenses_available (date,featureid,num_licenses,licid) values ('".$date."',".$featureid.",".$lic_number.",".$licid.")");
    }

    public function insert_used_licenses($featureid,$date,$time,$licenses_used,$licid) {
        mysql_query("insert into licenses_usage (featureid,date,time,users,licid) values (".$featureid.",'".$date."','".$time."',".$licenses_used.",".$licid.")");
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
