<?php

namespace HGC;

class ParameterHandler {
        static private $_instance=null;

        private $_parameters;
        private $_validationRegex;
        private $_mandatory;
        private $_defaults;
        private $_source;

        CONST SOURCE_POST=1;
        CONST SOURCE_GET=1;
        CONST SOURCE_REQUEST=1;

        static public function getInstance()
        {
                if (is_null(self::$_instance)) {
                        self::$_instance=new ParameterHandler();
                }

                return self::$_instance;
        }

        /**
         * @return int
         */
        public function getSource()
        {
                return $this->_source;
        }

        /**
         * @param int $source
         */
        public function setSource($source)
        {
                $valid=array(self::SOURCE_GET, self::SOURCE_POST, self::SOURCE_REQUEST);
                if (in_array($source, $valid)) {
                        $this->_source = $source;
                } else {
                        throw new \Exception('Invalid default source defined for parameters');
                }
        }

        public function __construct() {
                $this->_parameters=null;
                $this->_defaults=array();
                $this->setSource(self::SOURCE_REQUEST);
        }

        private function _setParameters($argParameters) {
                $this->_parameters=$argParameters;
                return $this;
        }

        private function _getParameters() {
                return $this->_parameters;
        }

        protected function _parse($argRawData=null) {
            global $argv;
            
            $lRawData = isset($argRawData) ? $argRawData : $argv;
            
            $lParams=array();
            
            if(!empty($lRawData)){
            	//throw new Exception(__FILE__.':'.__LINE__);
            	//if we have an argument or command line argument use that
            	// take parameter
                $i=0;
                
                while(@$lRawData[$i++]) {
                        $lParam=explode('=', trim($lRawData[$i-1], '-'));
                        if (count($lParam)==1) {
                                $lParams[$lParam[0]]=true;
                        } else {
                                $lParams[$lParam[0]]=$lParam[1];
                        }
                }
            } else {
                switch($this->_source) {
                        case self::SOURCE_REQUEST:
                                if(isset($_REQUEST) && !empty($_REQUEST)){
                                        //if we have a request use that
                                        $lParams = $_REQUEST;
                                }
                                break;
                        case self::SOURCE_GET:
                                if(isset($_GET) && !empty($_GET)){
                                        //if we have a request use that
                                        $lParams = $_GET;
                                }
                                break;
                        case self::SOURCE_POST:
                                if(isset($_POST) && !empty($_POST)){
                                        //if we have a request use that
                                        $lParams = $_POST;
                                }
                                break;
                        default:
                                throw(new \Exception('No recognised source for parameters'));
                                break;
                }
            }

            $this->_setParameters($lParams);
			return $this;
        }


        function getParameters() {
                if (is_null($this->_getParameters())) {
                        $this->_parse();
                }

                return $this->_getParameters();
        }

        public function setDefault($argKey, $argValue) {
                $this->_defaults[$argKey]=$argValue;
        }

        public function getDefaults() {
                return $this->_defaults;
        }

        public function getDefault($argKey) {
                if (array_key_exists($argKey, $this->_defaults)) {
                        return $this->_defaults[$argKey];
                }

                return null;
        }

        public function setValue($argKey, $argValue) {
                $this->getParameters();
                $this->_parameters[$argKey]=$argValue;
                return $this;
        }

        public function getValue($argKey) {
                if (array_key_exists($argKey, $this->getParameters())) {
                        return $this->_parameters[$argKey];
                }

                return $this->getDefault($argKey);
        }

        public function isValueSet($argKey) {
                $lValue=$this->getValue($argKey);
                return (!is_null($lValue));
        }

        public function setMandatory($argKey) {
            $this->_mandatory[$argKey]=true;
            return $this;
        }

        public function setValidationRegex($argKey, $argRegex) {
            $this->_validationRegex[$argKey]=$argRegex;
            return $this;
        }

        public function assertOK() {
            // ensure that all mandatory fields are set
            $issues=array();

            foreach($this->_mandatory as $key=>$value) {
                if (!$this->isValueSet($key)) {
                    $issues[]='The parameter `'.$key.'` was defined as being mandatory but was missing.';
                }
            }

            // ensure that all parameters match any regex requirements
            foreach($this->_validationRegex as $key=>$regex) {
                if (preg_match($regex, $this->getValue($key))!==1) {
                    $issues[]='The parameter `'.$key.' which had a value of `'.$this->getValue($key).'` did not mach the regex criteria of `'.$regex.'`';
                }
            }

            if (!empty($issues)) {
                throw new \Exception(var_export($issues, true));
            }

            return true;
        }
}

