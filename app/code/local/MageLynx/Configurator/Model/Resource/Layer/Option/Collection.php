<?php

class MageLynx_Configurator_Model_Resource_Layer_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{
    protected function _construct(){
        $this->_init('configurator/layer_option');
    }
}
