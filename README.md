translations
============

translation package


Usage :

<?php

// ---- CONFIGURATION ----

// get & configure storage
// a level 1 cache storage is automatically created by the system at instanciation
$translator = Translation_Manager::getInstance();
$translator->setDefaultLanguage('fr');

// adding 1st user storage
$redis = new Translation_Storage_Redis();
$translator->registerStorage('redis', $redis);

// adding 2nd user storage
$yaml = new Translation_Storage_Yaml();
$yaml->addFile('./test.yml');
$yaml->addFile('./test2.yml', true); // this file is flagged as writable (only one per Yaml Storage instance)
$translator->registerStorage('yaml', $yaml);


// ---- USAGE IN ACTION ----

// creating an alias for lazy-ass dev
function t($key, $lang = null) {
    return Translation_Manager::getInstance()->translate($key, $lang);
}

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

?><pre><?php var_dump(Translation_Manager::getInstance()->getNotFounds()); ?></pre><?php
?><pre><?php var_dump(Translation_Manager::getInstance()->dumpCacheLevel1()); ?></pre><?php
Translation_Manager::getInstance()->storeTranslation("pays", "ch", "payche"); // Rewrite test2.yml file with the new key/lang pair
