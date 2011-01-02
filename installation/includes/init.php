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

/*** include helper file ***/
include __SITE_PATH . '/includes/' . 'helper.php';

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
if(file_exists('../conf/config.php'))
{
    header('Location: ../index.php');
}
include (__SITE_PATH . '/config.php');

$registry->config = $config;
?>
