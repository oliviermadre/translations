<?php
class Translation_Storage_Redis extends Translation_Storage_Abstract implements Translation_Storage_Interface
{
    protected $redisInstance = null;
    protected $host = null;
    protected $port = null;
    protected $redisKnownKeys = 'REDIS_CACHE_KNOWN_KEYS';
    
    public function __construct($host = '127.0.0.1', $port = 6379)
    {
        $this->host = $host;
        $this->port = $port;
    }
    
    /**
     * @param Redis $instance
     * @return Translation_Storage_Redis
     */
    public function setRedisInstance(Redis $instance)
    {
        $this->redisInstance = $instance;
        return $this;
    }
    
    /**
     * @param string $value
     * @return Translation_Storage_Redis
     */
    public function setRedisKnownKeysKey($value)
    {
        $this->redisKnownKeys = $value;
        return $this;
    }
    
    public function init()
    {
        if (!$this->redisInstance && $this->host && $this->port) {
            $redisInstance = new Redis($this->host, $this->port);
            $this->setRedisInstance($redisInstance);
        }
        return $this;
    }
    
    
    public function get($key, $lang)
    {
        $this->init();
        $this->redisInstance->hGet($key, $lang);
        return false;
    }

    public function getAll()
    {
        $this->init();
        $aKeys = $this->redisInstance->hKeys($this->redisKnownKeys);
        $result = array();
        foreach ($aKeys as $key) {
            $result[$key] = $this->redisInstance->hGetAll($key);
        }
        return $result;
    }

    public function set($key, $lang, $value)
    {
        $this->init();
        $this->redisInstance->hSet($key, $lang, $value);
        $this->redisInstance->hSet($this->redisKnownKeys, $key, 1);
        return false;
    }

    public function deleteKeys($keys)
    {
    }

    public function invert($value, $lang)
    {
        return false;
    }
}
