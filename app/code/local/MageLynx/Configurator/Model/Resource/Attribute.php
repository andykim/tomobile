<?php
class MageLynx_Configurator_Model_Resource_Attribute extends Mage_Core_Model_Resource_Db_Abstract{        
    protected $_useIsObjectNew       = true;
    protected $_isPkAutoIncrement    = false;    
    
    protected function _construct(){
        $this->_init('configurator/attribute', 'attribute_id');
    }   
    
}