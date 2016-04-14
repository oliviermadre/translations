<?php

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;

class Translation_Manager
{
    const KEY_STORAGE_CACHE_LEVEL1 = 'cache_level1';

    private $locale;

    protected $storages = array();
    protected $notFounds = array();

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
        if (!array_key_exists($key, $this->notFounds)) {
            $this->notFounds[$key] = array();
        }

        if (!in_array($lang, $this->notFounds[$key])) {
            $this->notFounds[$key][] = $lang;
        }

        $this->storeTranslation($key, $lang, $key);

        return $this;
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
        $i = 1;
        reset($this->storages);
        while ($i < $depth) {
            $storage = current($this->storages);
            $storage->set($key, $lang, $translation);
            next($this->storages);
            $i++;
        }

        return true;
    }

    public function invert($value, $lang = null)
    {
        foreach ($this->storages as /* @var $storage Translation_Storage_Interface */ $storage) {
            $res = $storage->invert($value, $lang);
            if ($res !== false) {
                return $res;
            }
        }

        $this->logger->info('Unable to find revert translation', array('value' => $value, 'lang' => $lang));

        return $value;
    }

    /**
     * @param string $key
     * @param null   $locale
     * @return string
     */
    public function translate($key, $locale = null)
    {
        $translated = $key;
        $foundTranslation = false;
        $locale = ($locale) ? $locale : $this->locale;

        $depthStorages = 1;
        // try locale
        foreach ($this->storages as /* @var $storage Translation_Storage_Interface */ $storage) {

            $res = $storage->get($key, $locale);
            if ($res !== false) {
                $translated = $res;
                $foundTranslation = true;

                if ($depthStorages > 1) {
                    $this->storeTranslationIntoShallowStorages($depthStorages, $key, $locale, $translated);
                }

                break;
            }
            $depthStorages++;
        }
        if (!$foundTranslation && strlen($locale)== 5) {
            $depthStorages = 1;
            // try lang
            $lang = substr($locale, 0, 2);
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
        }

        if (!$foundTranslation) {
            $this->addNotFound($key, $locale);
        }

        // No, if key === $translated does not mean that not translated ...
        // if($key === $translated) {
        //     $this->logger->warning('Translation not found', array('key' => $key, 'lang' => $lang));
        //}

        return $translated;
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
        $ret = true;
        foreach ($this->storages as $keyStorage => /* @var $storage Translation_Storage_Interface */ $storage) {
            $res = $storage->set($key, $lang, $value);
            if (!$res) {
                $this->logger->error('Unable to store translation', array(
                    'key'     => $key,
                    'value'   => $value,
                    'lang'    => $lang,
                    'storage' => $keyStorage
                    ));
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     *
     * @param array $keys
     */
    public function deleteKeys($key, $lang, $value = "")
    {
        $ret = true;
        foreach ($this->storages as $keyStorage => /* @var $storage Translation_Storage_Interface */ $storage) {
            $res = $storage->deleteKeys(array($key));
            if (!$res) {
                $this->logger->error('Unable to delete translation', array(
                    'key'     => $key,
                    'value'   => $value,
                    'lang'    => $lang,
                    'storage' => $keyStorage
                    ));
                $ret = false;
            }
        }


        return $ret;
    }
}
