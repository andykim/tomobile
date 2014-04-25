<?php
class MageLynx_Configurator_Model_Source_Attribute extends Mage_Eav_Model_Entity_Attribute_Source_Table
{    
    public function getMultiselectOptions(){
        $return = array();
        foreach($this->getAllOptions() as $pair){
            $return[$pair['value']] = $pair['label'];
        }
        
        return $return;
    }
    public function getAllOptions(){
        if ($this->_options === null) {            
            
            $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter(Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId())
                ->addFieldToFilter('main_table.frontend_input', "select")
                ->addFieldToFilter('main_table.source_model', "eav/entity_attribute_source_table");
            
                        
            $this->_options = array();
            
            $request = Mage::app()->getFrontController()->getRequest();
            if($request->getModuleName() == 'admin' && $request->getControllerName() == 'system_config'){
                $this->_options[]= array(
                        'label' =>  'None',
                        'value' =>  ''
                    );
            }
        
            foreach($collection->getItems() as $_attribute){
                if($_attribute->getAttributeId() == Mage::getStoreConfig('configurator/settings/color')
                        && Mage::app()->getFrontController()->getRequest()->getControllerName() != 'system_config'){
                    continue;
                }
                                
                $this->_options[]= array(
                    'label' =>  $_attribute->getFrontendLabel(),
                    'value' =>  $_attribute->getAttributeId()
                );
            }
        }
                
            
        return $this->_options;
    }
    
    public function toOptionArray(){
        return $this->getAllOptions();
    }
}