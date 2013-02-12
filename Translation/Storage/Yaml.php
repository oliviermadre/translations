<?php
class Translation_Storage_Yaml extends Translation_Storage_Abstract implements Translation_Storage_Interface {
    protected $files = array();
    protected $isParsed = false;
    
    
    public function __construct() {
        
    }
    
    public function addFile($filepath) {
        $this->files[$filepath] = array();
        $this->isParsed = false;
    }
    
    public function init() {
        if (!$this->isParsed) {
            foreach($this->files as $key => $value) {
                if (file_exists($key)) {
                    $result = Yaml_Service::load($key);
                    if (is_array($result) && count($result) > 0) {
                        $this->files[$key] = $result;
                    }
                    else {
                        throw new RuntimeException("YAML Storage : can't parse file '" . $key ."'", E_USER_NOTICE);
                    }
                }
                else {
                    throw new LogicException("YAML Storage : wrong filepath given '" . $key . "'", E_USER_NOTICE);
                }
                
                $this->isParsed = true;
            }
        }
    }
    
    
    
    public function get($key, $lang) {
        $this->init();
        
        foreach($this->files as $file) {
            if (array_key_exists($key, $file) && array_key_exists($lang, $file[$key])) {
                return $file[$key][$lang];
            }
        }
        
        return false;
    }

    public function getAll() {
        $this->init();
        
        
        
        return false;
    }

    public function set($key, $lang, $value) {
        $this->init();
        return false;
    }
}