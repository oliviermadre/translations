<?php
class DummyVO {
    public $id = 0;
    public $translation_id = 0;
    public $name = '';
    
    protected $translationTable = array(
        'translation_id' => 'name'
    );
    
    public function __construct() {
        
    }
    
    public function populate(array $data = array()) {
        foreach($data as $key => $value) {
            if (array_key_exists($key, $this->translationTable)) {
                $mappedField = $this->translationTable[$key];
                $this->$mappedField = t($value);
            }
            
            $this->$key = $value;
        }
        
        return true;
    }
}