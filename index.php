<?php
date_default_timezone_set('Europe/Paris');
ini_set('display_errors', false);

require_once './Initialize.php';

spl_autoload_register(function($className) {
   $str = str_replace('_', '/', $className);
    if (file_exists('./' . $str . '.php')) {
        require_once './' . $str . '.php';
        return true;
    }
    return false;
});

function t($key, $lang = null) {
    return Translation_Manager::getInstance()->translate($key, $lang);
}

// CODE STARTS HERE
$initializer = new Initializer();

echo "# XX : " . t("maison") . "#" . '<br />';
echo "# FR : " . t("maison", 'fr') . "#" . '<br />';
echo "# EN : " . t("maison", 'en') . "#" . '<br />';
echo "# ES : " . t("maison", 'es') . "#" . '<br />';

?><pre><?php var_dump(Translation_Manager::getInstance()->getNotFounds()); ?></pre><?php


?><pre><?php var_dump(Translation_Manager::getInstance()->dumpCacheLevel1()); ?></pre><?php
