<?php
class MageLynx_Configurator_Model_Resource_Layer extends Mage_Core_Model_Resource_Db_Abstract{        
    protected function _construct(){
        $this->_init('configurator/layer', 'layer_id');
    }        
}