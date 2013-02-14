<?php
class Translation_Manager {
    const KEY_STORAGE_CACHE_LEVEL1 = 'cache_level1';
    protected $storages = array();
    protected $defaultLanguage = null;
    protected $notFounds = array();
    
    /**
     * @var Translation_Manager 
     */
    private static $instance = null;
    
    /**
     * 
     * @return Translation_Manager
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new static();
            self::$instance->registerStorage(self::KEY_STORAGE_CACHE_LEVEL1, new Translation_Storage_RuntimeArray());
        }
        return self::$instance;
    }
    
    /**
     * 
     * @param string $name
     * @param Translation_Storage_Interface $instance
     */
    public function registerStorage($name, Translation_Storage_Interface $instance) {
        $this->storages[$name] = $instance;
    }
    
    /**
     * @param string $lang
     * @return Translation_Manager
     * @throws LogicException
     */
    public function setDefaultLanguage($lang) {
        if (!$lang || !is_string($lang)) {
            throw new LogicException('Language must be a string, usually an iso2 code');
        }
        
        $this->defaultLanguage = $lang;

        return $this;
    }
    
    /**
     * @return array
     */
    public function dumpCacheLevel1() {
        return $this->getCacheLevel1()->getAll();
    }
    
    /**
     * 
     * @return Translation_Storage_RuntimeArray
     */
    protected function getCacheLevel1() {
        return $this->storages[self::KEY_STORAGE_CACHE_LEVEL1];
    }
    
    /**
     * @param string $key
     * @param string $lang
     */
    protected function addNotFound($key, $lang) {
        trigger_error("Translation error, key not found '" . $key . "' (lang = '" . $lang . "')", E_USER_NOTICE);
        if (!array_key_exists($key, $this->notFounds)) {
            $this->notFounds[$key] = array();
        }
        
        $this->notFounds[$key][] = $lang;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getNotFounds() {
        return $this->notFounds;
    }
    
    /**
     * @return int
     */
    public function countNotFounds() {
        return count($this->notFounds);
    }
    
    /**
     * 
     * @param int $depth
     * @param string $key
     * @param string $lang
     * @param string $translation
     * @return boolean
     */
    protected function storeTranslationIntoShallowStorages($depth, $key, $lang, $translation) {
        $i = 1;
        reset($this->storages);
        while($i < $depth) {
            $storage = current($this->storages);
            $storage->set($key, $lang, $translation);
            next($this->storages);
            $i++;
        }
        return true;
    }
    
    /**
     * @param string $key
     * @param string $lang
     */
    public function translate($key, $lang = null) {
        $translated = $key;
        $foundTranslation = false;
        $lang = ($lang) ? $lang : $this->defaultLanguage;
        
        $depthStorages = 1;
        foreach($this->storages as /* @var $storage Translation_Storage_Interface */ $storage) {
            $res = $storage->get($key, $lang);
            if ($res !== false) {
                $translated = $res;
                $foundTranslation = true;
                
                if ($depthStorages > 1) {
                    $this->storeTranslationIntoShallowStorages($depthStorages, $key, $lang, $translated);
                }
                
                break;
            }
            
            $depthStorages++;
        }
        
        if (!$foundTranslation) {
            $this->addNotFound($key, $lang);
        }

        return $translated;
    }
    
    
    /**
     * 
     * @param string $key
     * @param string $lang
     * @param string $value
     * @return boolean
     */
    public function storeTranslation($key, $lang, $value = "") {
        $ret = true;
        foreach($this->storages as $keyStorage => /* @var $storage Translation_Storage_Interface */ $storage) {
            $res = $storage->set($key, $lang, $value);
            if (!$res) {
                trigger_error("Translation of '" . $key . "' ( " . $lang . ") = '" . $value . "' not saved in storage '" . $keyStorage . "'", E_USER_NOTICE);
                $ret = false;
            }
        }
        return $ret;
    }
}