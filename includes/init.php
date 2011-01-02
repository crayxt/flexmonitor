<?php
 /*
  * ** include the controller class ***/
 include __SITE_PATH . '/application/' . 'controller_base.class.php';

 /*** include the registry class ***/
 include __SITE_PATH . '/application/' . 'registry.class.php';

 /*** include the router class ***/
 include __SITE_PATH . '/application/' . 'router.class.php';

 /*** include the template class ***/
 include __SITE_PATH . '/application/' . 'template.class.php';



 /*** auto load model classes ***/
 function __autoload($class_name) {
    $filename = strtolower($class_name) . '.class.php';
    $file = __SITE_PATH . '/model/' . $filename;

    if (file_exists($file) == false)
    {
        return false;
    }
  include ($file);
}

 /*** a new registry object ***/
 $registry = new registry;

/*** include config file ***/
$conf_file = __SITE_PATH . '/conf/config.php';
if(file_exists($conf_file))
{
    include $conf_file;
}

 /*** create the database registry object ***/
 $registry->config = $config;
 $registry->lmutil = $lmutil;
 $registry->dbinfo = $db;
 $registry->db = DB::getInstance($registry);
 $registry->licengine = LicEngine::getInstance($registry);
?>
