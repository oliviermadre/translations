<?php
interface Translation_Storage_Interface {
    /**
     * Retrieve a key from the storage
     * @param string $key
     * @param string $lang
     */
    public function get($key, $lang);
    
    /**
     * Stores a key in the storage
     * @param string $key
     * @param string $lang
     * @param mixed $value
     */
    public function set($key, $lang, $value);

    /**
     * Retrieve all the key from the storage
     */
    public function getAll();
    
}