<?php
Class template {
    /*
    * @the registry
    * @access private
    */
    private $registry;

    /*
    * @Variables array
    * @access private
    */
    private $vars = array();

    /**
    *
    * @constructor
    *
    * @access public
    *
    * @return void
    *
    */
    function __construct($registry) {
        $this->registry = $registry;

    }

    /**
    *
    * @set undefined vars
    *
    * @param string $index
    *
    * @param mixed $value
    *
    * @return void
    *
    */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    function show($name) {
        $header = __SITE_PATH . '/views/site_header.php';
        $footer = __SITE_PATH . '/views/site_footer.php';
        $path = __SITE_PATH . '/views' . '/' . $name . '.php';
        if (file_exists($path) == false)
        {
                throw new Exception('Template not found in '. $path);
                return false;
        }
        $this->config = $this->registry->config;
        // Load variables
        foreach ($this->vars as $key => $value)
        {
                $$key = $value;
        }
        include ($header);
        include ($path);
        include ($footer);
    }


}

?>
