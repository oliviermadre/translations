<?php

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;

class Translation_Manager
{
    const KEY_STORAGE_CACHE_LEVEL1 = 'cache_level1';

    private $locale;

    protected $storages = array();
    protected $notFounds = array();
    protected $enableNotice = true;

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @var Translation_Manager
     */
    private static $instance = null;

    /**
     *
     * @return Translation_Manager
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
            self::$instance->setLogger(new NullLogger());
            self::$instance->registerStorage(self::KEY_STORAGE_CACHE_LEVEL1, new Translation_Storage_RuntimeArray());
        }
        return self::$instance;
    }

    /**
     * @param LoggerInterface $logger
     * @return Translation_Manager
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     *
     * @param string $name
     * @param Translation_Storage_Interface $instance
     */
    public function registerStorage($name, Translation_Storage_Interface $instance)
    {
        $this->storages[$name] = $instance;
    }

    /**
     * @param string $lang
     * @return Translation_Manager
     * @throws LogicException
     */
    public function setDefaultLanguage($lang)
    {
        $this->setLocale($lang);

        return $this;
    }

    /**
     * Set the current locale
     *
     * @param  string          $locale      the application locale
     * @throws LogicException               the locale is null or is not a string
     */
    public function setLocale($locale)
    {
        if (!$locale || !is_string($locale)) {
            throw new LogicException('Language must be a string, usually an iso2 code');
        }

        $this->locale = $locale;
    }

    /**
     * Get the locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function setEnableNotice($bool)
    {
        $this->enableNotice = (bool)$bool;
        return $this;
    }

    /**
     * @return array
     */
    public function dumpCacheLevel1()
    {
        return $this->getCacheLevel1()->getAll();
    }

    /**
     *
     * @return Translation_Storage_RuntimeArray
     */
    protected function getCacheLevel1()
    {
        return $this->storages[self::KEY_STORAGE_CACHE_LEVEL1];
    }

    /**
     * @param string $key
     * @param string $lang
     */
    protected function addNotFound($key, $lang)
    {
        try {
            if (!$this->enableNotice) {
                set_error_handler(function ($errno, $errstr, $errfile, $errline) {});
            }

            trigger_error("Translation error, key not found '" . $key . "' (lang = '" . $lang . "')", E_USER_NOTICE);
            if (!array_key_exists($key, $this->notFounds)) {
                $this->notFounds[$key] = array();
            }

            if (!in_array($lang, $this->notFounds[$key])) {
                $this->notFounds[$key][] = $lang;
            }

            if (!$this->enableNotice) {
                restore_error_handler();
            }

            $this->storeTranslation($key, $lang, $key);

            return $this;
        } catch (Exception $e) {
            if (!$this->enableNotice) {
                restore_error_handler();
            }

            throw $e;
        }
    }

    /**
     * @return array
     */
    public function getNotFounds()
    {
        return $this->notFounds;
    }

    /**
     * @return int
     */
    public function countNotFounds()
    {
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
    protected function storeTranslationIntoShallowStorages($depth, $key, $lang, $translation)
    {
        try {
            if (!$this->enableNotice) {
                set_error_handler(function ($errno, $errstr, $errfile, $errline) {});
            }

            $i = 1;
            reset($this->storages);
            while ($i < $depth) {
                $storage = current($this->storages);
                $storage->set($key, $lang, $translation);
                next($this->storages);
                $i++;
            }

            if (!$this->enableNotice) {
                restore_error_handler();
            }

            return true;
        } catch (Exception $e) {
            if (!$this->enableNotice) {
                restore_error_handler();
            }

            throw $e;
        }
    }

    public function invert($value, $lang = null)
    {
        foreach ($this->storages as /* @var $storage Translation_Storage_Interface */ $storage) {
            $res = $storage->invert($value, $lang);
            if ($res !== false) {
                return $res;
            }
        }

        if (!$this->enableNotice) {
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {});
        }

        trigger_error("Couldn't find revert translation for '" . $value . "' in lang '" . $lang . "'", E_USER_NOTICE);

        if (!$this->enableNotice) {
            restore_error_handler();
        }

        return $value;
    }

    /**
     * @param string $key
     * @param string $lang
     */
    public function translate($key, $lang = null)
    {
        try {
            if (!$this->enableNotice) {
                set_error_handler(function ($errno, $errstr, $errfile, $errline) {});
            }

            $translated = $key;
            $foundTranslation = false;
            $lang = ($lang) ? $lang : $this->locale;

            $depthStorages = 1;
            foreach ($this->storages as /* @var $storage Translation_Storage_Interface */ $storage) {
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

            if($key === $translated) {
                $this->logger->warning('Translation not found', array('key' => $key, 'lang' => $lang));
            }

            if (!$this->enableNotice) {
                restore_error_handler();
            }

            return $translated;
        } catch (Exception $e) {
            if (!$this->enableNotice) {
                restore_error_handler();
            }

            throw $e;
        }
    }

    /**
     *
     * @param string $key
     * @param string $lang
     * @param string $value
     * @return boolean
     */
    public function storeTranslation($key, $lang, $value = "")
    {
        try {
            if (!$this->enableNotice) {
                set_error_handler(function ($errno, $errstr, $errfile, $errline) {});
            }

            $ret = true;
            foreach ($this->storages as $keyStorage => /* @var $storage Translation_Storage_Interface */ $storage) {
                $res = $storage->set($key, $lang, $value);
                if (!$res) {
                    trigger_error("Translation of '" . $key . "' ( " . $lang . ") = '" . $value . "' not saved in storage '" . $keyStorage . "'", E_USER_NOTICE);
                    $ret = false;
                }
            }

            if (!$this->enableNotice) {
                restore_error_handler();
            }


            return $ret;
        } catch (Exception $e) {
            if (!$this->enableNotice) {
                restore_error_handler();
            }

            throw $e;
        }
    }

    /**
     *
     * @param array $keys
     */
    public function deleteKeys($key, $lang, $value = "")
    {
        try {
            if (!$this->enableNotice) {
                set_error_handler(function ($errno, $errstr, $errfile, $errline) {});
            }

            $ret = true;
            foreach ($this->storages as $keyStorage => /* @var $storage Translation_Storage_Interface */ $storage) {
                $res = $storage->deleteKeys($keys);
                if (!$res) {
                    trigger_error("Unable of '" . $key . "' ( " . $lang . ") = '" . $value . "' not saved in storage '" . $keyStorage . "'", E_USER_NOTICE);
                    $ret = false;
                }
            }

            if (!$this->enableNotice) {
                restore_error_handler();
            }


            return $ret;
        } catch (Exception $e) {
            if (!$this->enableNotice) {
                restore_error_handler();
            }

            throw $e;
        }
    }
}
