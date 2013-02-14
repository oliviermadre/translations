<?php
date_default_timezone_set('Europe/Paris');
ini_set('display_errors', true);

require_once './Initialize.php';

spl_autoload_register(function($className) {
   $str = str_replace('_', '/', $className);
   $str = str_replace('\\','/', $str);
   $str = str_replace('Symfony/Component/', '', $str);
   
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
echo '<br />';

echo "# XX : " . t("pays") . "#" . '<br />';
echo "# FR : " . t("pays", 'fr') . "#" . '<br />';
echo "# EN : " . t("pays", 'en') . "#" . '<br />';
echo "# ES : " . t("pays", 'es') . "#" . '<br />';
echo "# IT : " . t("pays", 'it') . "#" . '<br />';
echo "# DE : " . t("pays", 'de') . "#" . '<br />';
echo "# CH : " . t("pays", 'ch') . "#" . '<br />';

$vo1 = new DummyVO();
$vo1->populate(array('id' => 1, 'translation_id' => 100));


echo t($vo1->name, 'de') . '<br />';
echo t($vo1->name, 'fr') . '<br />';
echo t($vo1->name, 'es') . '<br />';
echo t($vo1->name, 'it') . '<br />';



?><pre><?php var_dump(Translation_Manager::getInstance()->getNotFounds()); ?></pre><?php


?><pre><?php var_dump(Translation_Manager::getInstance()->dumpCacheLevel1()); ?></pre><?php

?><pre><?php var_dump($vo1); ?></pre><?php

Translation_Manager::getInstance()->storeTranslation("pa:ys", "ch", "pay:che");