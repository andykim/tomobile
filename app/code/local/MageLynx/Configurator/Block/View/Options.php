<?php

class MageLynx_Configurator_Block_View_Options extends MageLynx_Configurator_Block_Abstract{            
    public function __construct() {
        parent::__construct();
        
        $attributes_ids = explode("," , Mage::registry('product')->getConfiguratorAttributes());
        
        $_attribute_collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('main_table.`attribute_id`', array('in'=>$attributes_ids));
        
        $this->setAttributeCollection($_attribute_collection);
    }
    
    public function getOptionCollection($attribute){
        return Mage::helper('configurator')->getOptionCollection($attribute);
    }
    
    public function getPredefinedData(){        
        $_product = $this->getProduct();
        
        $configurator_data = Zend_Json::decode($_product->getData("configurator_options"));
        if(!is_array($configurator_data)){
            $configurator_data = array();
        }                
        
        return new Varien_Object($configurator_data);
    }
    
    public function getColorAttribute(){
        return Mage::helper('configurator')->getColorAttribute();
    }
    
    
}