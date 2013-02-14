<?php
class Translation_Storage_Yaml extends Translation_Storage_Abstract implements Translation_Storage_Interface {
    protected $files = array();
    protected $isParsed = false;
    protected $fileWritable = null;
    protected $fileWritableYamlDATA = null;
    
    public function __construct() {
        
    }
    
    public function addFile($filepath, $isWritable = false) {
        if (file_exists($filepath)) {
            $this->files[$filepath] = array();
            $this->isParsed = false;
            
            if ($isWritable && is_writable($filepath)) {
                $this->fileWritable = $filepath;
                $this->fileWritableYamlDATA = null;
            }
            else if ($isWritable)  {
                throw new RuntimeException("Can't set the given file writable (given '" . $filepath . "')");
            }
        }
        else {
            throw new RuntimeException("Invalid file given (given '" . $filepath . "')");
        }
    }
    
    public function init() {
        if (!$this->isParsed) {
            foreach($this->files as $key => $value) {
                if (file_exists($key)) {
                    try {
                        $result = Yaml_Service::load($key);
                        $this->files[$key] = $result;
                    }
                    catch (Exception $e) {
                        throw new RuntimeException("YAML Storage : can't parse file '" . $key ."'");
                    }
                }
                else {
                    throw new LogicException("YAML Storage : wrong filepath given '" . $key . "'");
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
        $output = array();
        foreach($this->files as $file) {
            $output = array_merge($output, $file);
        }
        return $output;
    }

    public function set($key, $lang, $value) {
        $this->init();
        
        try {
            if ($this->fileWritable) {
                if (!$this->fileWritableYamlDATA) {
                    $yamlData = Yaml_Service::load($this->fileWritable);
                    $this->fileWritableYamlDATA = $yamlData;
                }
                
                
                if (!array_key_exists($key, $this->fileWritableYamlDATA)) {
                    $this->fileWritableYamlDATA[$key] = array();
                }
                
                if (!array_key_exists($key, $this->files[$this->fileWritable])) {
                    $this->files[$this->fileWritable][$key] = array();
                }
                
                $this->fileWritableYamlDATA[$key][$lang] = $value;
                $this->files[$this->fileWritable][$key][$lang] = $value;
                
                $yamlToWrite = Yaml_Service::dump($this->fileWritableYamlDATA);
                
                
                $res = file_put_contents($this->fileWritable, $yamlToWrite);
                if (!$res) {
                    throw new RuntimeException("Couldn't write to YAML file");
                }
                return true;
            }
            else {
                throw new RuntimeException("No writable file for YAML Storage");
            }
            
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage, $e->getCode(), $e);
        }
        
        return true;
    }
}
