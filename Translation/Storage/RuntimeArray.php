<?php
class Translation_Storage_RuntimeArray extends Translation_Storage_Abstract implements Translation_Storage_Interface {
    protected $array = array();
    
    /**
     * 
     * @param string $key
     * @param string $lang
     * @return string|boolean
     */
    public function get($key, $lang) {
        if (array_key_exists($lang, $this->array)) {
            if (array_key_exists($key, $this->array[$lang])) {
                return $this->array[$lang][$key];
            }
        }
        
        return false;
    }

    /**
     * 
     * @return array
     */
    public function getAll() {
        return $this->array;
    }

    /**
     * 
     * @param string $key
     * @param string $lang
     * @param string $value
     * @return boolean
     */
    public function set($key, $lang, $value) {
        if (!array_key_exists($lang, $this->array)) {
            $this->array[$lang] = array();
        }
        
        $this->array[$lang][$key] = $value;
        
        return true;
    }
}