<?php
class Initializer {
    public function __construct() {
        $translator = Translation_Manager::getInstance();
        $translator->setDefaultLanguage('fr');
//        $translator->registerStorage('redis', new Translation_Storage_Redis());
        
        $yaml = new Translation_Storage_Yaml();
        $yaml->addFile('./test.yml');
        
        $translator->registerStorage('yaml', $yaml);
        
    }
}