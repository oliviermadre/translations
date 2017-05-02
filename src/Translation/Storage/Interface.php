<?php
interface Translation_Storage_Interface
{
    /**
     * Retrieve a key by its translation from the storage
     * @param string $value
     * @param string $lang
     */
    public function invert($value, $lang);
    /**
     * Retrieve a translation from the storage
     * @param string $key
     * @param string $lang
     */
    public function get($key, $lang);
    
    /**
     * Stores a translation in the storage
     * @param string $key
     * @param string $lang
     * @param mixed $value
     */
    public function set($key, $lang, $value);

    /**
     * Retrieve all the translations from the storage
     */
    public function getAll();
    
    /**
     * Delete all the stored keys
     * @param array $keys
     */
    public function deleteKeys($keys);
}
