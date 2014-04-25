<?php

class MageLynx_Configurator_Model_Resource_Layer_Option extends Mage_Core_Model_Resource_Db_Abstract {
    protected $_fields = array();
    
    protected function _construct(){
        $this->_init('configurator/layer_option', 'option_id');
    }  
    
    public function getFields(){
        if(empty($this->_fields)){
            $columns = $this->_getReadAdapter()->describeTable($this->getMainTable());
            foreach($columns as $fieldname => $column){                
                $this->_fields[$fieldname]=$column;
            }
        }
        return $this->_fields;
    }
}