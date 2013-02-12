<?php
class Translation_Storage_Redis extends Translation_Storage_Abstract implements Translation_Storage_Interface {
    protected $redisInstance = null;
    protected $host = null;
    protected $port = null;
    
    public function __construct($host = '127.0.0.1', $port = 6379) {
        $this->host = $host;
        $this->port = $port;
    }
    
    /**
     * 
     * @param Redis $instance
     * @return Translation_Storage_Redis
     */
    public function setRedisInstance(Redis $instance) {
        $this->redisInstance = $instance;
        return $this;
    }
    
    public function init() {
        if (!$this->redisInstance && $this->host && $this->port) {
            $redisInstance = new Redis($this->host, $this->port);
            $this->setRedisInstance($redisInstance);
        }
        return $this;
    }
    
    
    public function get($key, $lang) {
        $this->init();
        
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