<?php
class Translation_Storage_CustomDatabase extends Translation_Storage_Abstract implements Translation_Storage_Interface {
    /**
     * 
     * @param string $key
     * @param string $lang
     * @return string|boolean
     */
    public function get($key, $lang) {
        // FETCH INTO TABLE TRANSLATIONS
        if (is_numeric($key)) {
            // do query
        }
        
        return false;
    }

    /**
     * 
     * @return array
     */
    public function getAll() {
        // FETCH ALL THE TRANSLATIONS TABLE
        
        return array();
    }

    /**
     * 
     * @param string $key
     * @param string $lang
     * @param string $value
     * @return boolean
     */
    public function set($key, $lang, $value) {
        // INSERT INTO TRANSLATIONS TABLE
        
        
        
        return true;
    }
}