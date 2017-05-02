<?php
class Translation_Storage_RuntimeArray extends Translation_Storage_Abstract implements Translation_Storage_Interface
{
    protected $array = array();
    
    /**
     *
     * @param string $key
     * @param string $lang
     * @return string|boolean
     */
    public function get($key, $lang)
    {
        if (array_key_exists($key, $this->array)) {
            if (array_key_exists($lang, $this->array[$key])) {
                return $this->array[$key][$lang];
            }
        }
        
        return false;
    }

    /**
     *
     * @return array
     */
    public function getAll()
    {
        return $this->array;
    }

    /**
     *
     * @param string $key
     * @param string $lang
     * @param string $value
     * @return boolean
     */
    public function set($key, $lang, $value)
    {
        if (!array_key_exists($key, $this->array)) {
            $this->array[$key] = array();
        }
        
        $this->array[$key][$lang] = $value;
        
        return true;
    }

    public function deleteKeys($keys)
    {
        // Todo : to be implemented
    }

    public function invert($value, $lang)
    {
        foreach ($this->array as $key => $langs) {
            if (array_key_exists($lang, $langs)) {
                if (in_array($value, $langs[$lang])) {
                    return $key;
                }
            }
        }
        return false;
    }
}
