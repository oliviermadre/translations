<?php
class EvaneosDbTranslationStorage extends Translation_Storage_Abstract implements Translation_Storage_Interface {
    const REPLACE_PATTERN = 'TR:DB:STORE:';
    const KEY_ID = 0;
    
    protected $requestedKeys = array();
    protected $resultingValues = array();
    
    public function get($key, $lang) {
        if (is_numeric($key)) {
            $tempKey = self::REPLACE_PATTERN . $key;
            $this->requestedKeys[$tempKey] = array(self::KEY_ID => $key);
            return $tempKey;
        }
        elseif (array_key_exists($key, $this->requestedKeys)) {
            if (!array_key_exists($key, $this->resultingValues)) {
                $this->fetchFromDatabase();
            }
            
            if (array_key_exists($key, $this->resultingValues)) {
                if (array_key_exists($lang, $this->resultingValues[$key])) {
                    return $this->resultingValues[$key][$lang];
                }
            }
            return false;
        }
        else {
            return false;
        }
    }
    
    protected function fetchFromDatabase() {
        mysql_connect('localhost', 'root', '');
        mysql_select_db('evaneos');
        
        $criterias = array();
        foreach($this->requestedKeys as $data) {
            $criteria = "(id=" . $data[self::KEY_ID] . ")";
            $criterias[] = $criteria;
        }
        $implodeWhere = implode(' OR ', $criterias);
        
        $qry = <<<SQL
SELECT
    id,
    name,
    description,
    iso2
FROM
    translations
WHERE
    $implodeWhere
SQL;
        $res = mysql_query($qry);
        while($arr = mysql_fetch_assoc($res)) {
            $key = self::REPLACE_PATTERN . $arr['id'];
            if (!array_key_exists($key, $this->resultingValues)) {
                $this->resultingValues[$key] = array();
            }
            $this->resultingValues[$key][$arr['iso2']] = $arr['name'];
        }
        
        mysql_close();
        return true;
    }
    

    public function getAll() {
        
    }

    public function set($key, $lang, $value) {
        if (!is_numeric($key)) {
            return true;
        }
    }
}