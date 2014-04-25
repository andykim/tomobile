<?php

class MageLynx_Configurator_Model_Resource_Attribute_Option_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection{
    protected function _initSelect(){
        parent::_initSelect();
        
        //add values        
        $this->getSelect()->joinLeft(
                    array('option_value_table' => Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value')),
                    'option_value_table.option_id = main_table.option_id AND option_value_table.store_id = 0');
        
        //add configurator fields
        $_alias = 'configurator';                
        $table = Mage::getResourceModel("configurator/attribute_option")->getMainTable();
                
        $this->getSelect()
                ->joinLeft(
                    array($_alias => $table),
                    "`$_alias`.`option_id` = `main_table`.`option_id` AND `$_alias`.`attribute_id` = `main_table`.`attribute_id`");               
        
        return $this;
    }
    
    protected function _beforeLoad(){
        $this->setModel('configurator/attribute_option');
        
        return parent::_beforeLoad();
    }
}
