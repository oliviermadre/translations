<?php
class Translation_Storage_Memcached extends Translation_Storage_Abstract implements Translation_Storage_Interface
{
    protected $memcached = null;
    protected $host = null;
    protected $port = null;

    protected $mcKnownKeys = 'MEMCACHED_CACHE_KNOWN_KEYS';
    protected $mcPrefixKey = 'TR:MC:STORE:';

    public function __construct(Memcached $instance = null)
    {
        $this->memcached = $instance;
    }

    /**
     *
     * @param Memcached $instance
     * @return Translation_Storage_Memcached
     */
    public function setMemcachedInstance(Memcached $instance)
    {
        $this->memcached = $instance;
        return $this;
    }

    /**
     * @param string $value
     * @return Translation_Storage_Memcached
     */
    public function setMemcachedKnownKeysKey($value)
    {
        $this->mcKnownKeys = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Translation_Storage_Memcached
     */
    public function setMemcachedPrefixKey($value)
    {
        $this->mcPrefixKey = $value;
        return $this;
    }

    public function init()
    {
        if (!$this->memcached) {
            throw new RuntimeException("Couldn't initialize memcached");
        }

        return $this;
    }

    private function makeKey($key, $lang)
    {
        return $this->mcPrefixKey . $lang . ':' . md5($key);
    }

    public function get($key, $lang)
    {
        $this->init();
        $memcachedKey = $this->makeKey($key, $lang);
        $md5Key = md5($memcachedKey);
        $res = $this->memcached->get($md5Key);
        if ($this->memcached->getResultCode() !== Memcached::RES_NOTFOUND) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * DO NOT USE THIS METHOD
     * @return array
     * @deprecated since version 5.1
     */
    public function getAll()
    {
        $this->init();

        return array();
        /*
        $aKeys = $this->memcached->get($this->mcKnownKeys);
        $result = array();
        foreach($aKeys as $key) {
            $result[$key] = $this->memcached->get($key);
        }
        return $result;
        */
    }

    public function set($key, $lang, $value)
    {
        $this->init();
        $memcachedKey = $this->makeKey($key, $lang);
        $md5Key = md5($memcachedKey);
        $this->memcached->set($md5Key, $value);

        if ($this->memcached->getResultCode() === Memcached::RES_SUCCESS) {
            return true;
        }

        return false;
    }

    public function deleteKeys($keys)
    {
        // Todo : to be implemented
    }

    public function invert($value, $lang)
    {
        return false;
    }
}
