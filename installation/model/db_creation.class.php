<?php
class db_creation {
    private $dbinfo = array();
    private $con;
    private $dbcreated = false;
    private $dbupdate = false;
    private $dbuse = false;

    public function __construct($dbinfo)
    {
        $this->dbinfo['server'] = $dbinfo['server'];
        $this->dbinfo['user'] = $dbinfo['user'];
        $this->dbinfo['pass'] = $dbinfo['pass'];
        $this->dbinfo['dbname'] = $dbinfo['dbname'];
        $this->dbinfo['type'] = $dbinfo['type'];
        switch ($dbinfo['type']) {
            case 'create':
                break;
            case 'update':
                $this->dbcreated = true;
                $this->dbupdate = true;
                break;
            case 'use':
                $this->dbcreated = true;
                $this->dbuse = true;
                break;
        }
    }

    private function connect()
    {
        if(!($this->con = @mysql_connect($this->dbinfo['server'], $this->dbinfo['user'], $this->dbinfo['pass'])))
        {
            return false;
        }else{
            return mysql_query("SET NAMES 'utf8'");
        }
    }

    private function createdb()
    {
        if(!$this->connect()){
            return false;
        }else{
            if($this->dbcreated)
            {
                return true;
            }else{
                if(!mysql_query("create database " . $this->dbinfo['dbname']))
                {
                    return false;
                }else{
                    $this->dbcreated = true;
                    return true;
                }
            }
        }
    }

    private function selectdb()
    {
        if(!$this->createdb())
        {
            return false;
        }else{
            return mysql_select_db($this->dbinfo['dbname'], $this->con);
        }
    }

    private function populatedb()
    {
        if($this->dbuse)
        {
            return true;
        }else{
            if($this->dbupdate)
            {
                $sqlfile = __SITE_PATH . '/mysql/update.sql';
            }else{
                $sqlfile = __SITE_PATH . '/mysql/database.sql';
            }
            if(!$buffer = file_get_contents($sqlfile))
            {
                return false;
            }else{
                $queries = InstallationHelper::splitSql($buffer);
                $dberror = 0;
                foreach ($queries as $query)
                {
                    $query = trim($query);
                    if ($query != '' && $query {0} != '#')
                    {
                        if(!mysql_query($query))
                        {
                            $dberror++;
                        }
                    }
                }
                if($dberror>0){
                    return false;
                }else{
                    return true;
                }
            }
        }
    }
    
    private function closedb()
    {
        return mysql_close($this->con);
    }

    public function makedb()
    {
        if(!$this->selectdb())
        {
            return false;
        }else{
            if(!$this->populatedb())
            {
                return false;
            }else{
                return $this->closedb();
            }
        }
    }
}
?>
