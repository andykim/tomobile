<?php
class MageLynx_Configurator_Model_Resource_Attribute_Option extends Mage_Core_Model_Resource_Db_Abstract{
    protected $_useIsObjectNew       = true;
    protected $_isPkAutoIncrement    = false;    
    
    protected $_join_fields = array();
    
    protected function _construct(){
        $this->_init('configurator/attribute_option', 'option_id');
    }
    
    public function getJoinFields(){
        if(empty($this->_join_fields)){
            $columns = $this->_getReadAdapter()->describeTable($this->getMainTable());
            foreach($columns as $fieldname => $column){                
                $this->_join_fields[$fieldname]=$column;
            }
        }
        return $this->_join_fields;        
    }
}