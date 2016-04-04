<?php


/**
 * Class ConfigHandler
 */
class ConfigHandler
{
    private $_config;

    static private $_instance=null;

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new ConfigHandler();
        }

        return self::$_instance;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    public function __construct() {
        $this->setConfig(array());
    }

    public function init($argFilename) {
        $raw_config=parse_ini_file($argFilename, true);

        if (array_key_exists($_SERVER['HTTP_HOST'], $raw_config)) {
            $this->setConfig($raw_config[$_SERVER['HTTP_HOST']]);
        } else {
            syslog(LOG_ERR, 'Unable to find config for HTTP_HOST='.$_SERVER['HTTP_HOST']);
            die();
        }
    }

    public function getValue($argKey, $argDefault=null) {
        if (!array_key_exists($argKey, $this->getConfig())) {
            if (is_null($argDefault)) {
                throw new Exception('Request for missing config parameter (' . $argKey . ') received when no default provided.');
            } else {
                return $argDefault;
            }
        }
        return $this->_config[$argKey];
    }
}
