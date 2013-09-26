<?php
abstract class Translation_Storage_Abstract implements Translation_Storage_Interface {
    /**
     * @param Translation_Storage_Interface $storage
     */
    public function import(Translation_Storage_Interface $storage) {
        $map = $storage->getAll();
        foreach($map as $lang => $datas) {
            foreach($datas as $key => $value) {
                $this->set($key, $lang, $value);
            }
        }
        
        return true;
    }
}
