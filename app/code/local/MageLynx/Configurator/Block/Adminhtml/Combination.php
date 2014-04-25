<?php

class MageLynx_Configurator_Block_Adminhtml_Combination extends Mage_Adminhtml_Block_Template{
        
    public function getAttributesJson(){
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter(Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId())
            ->addFieldToFilter('main_table.frontend_input', "select")
            ->addFieldToFilter('main_table.source_model', "eav/entity_attribute_source_table");
        
        
        $result = array();
        foreach($collection->getItems() as $attribute){
            try{ 
                $options = array();
                foreach($attribute->getSource()->getAllOptions(false) as $option){
                    if(isset($option['value']) && is_string($option['value'])){                        
                        $options[$option['value']] = $option['label'];
                    }                    
                };                
                $result[$attribute->getId()] =  array(
                        'frontend_label'    =>  $attribute->getData('frontend_label'),
                        'options'           =>  $options
                    );
            }catch(Exception $e){};
            
        }
        
        return Zend_Json::encode($result);
    }
}