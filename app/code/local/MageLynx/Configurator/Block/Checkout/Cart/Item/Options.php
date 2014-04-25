<?php

class MageLynx_Configurator_Block_Checkout_Cart_Item_Options extends Mage_Core_Block_Template{
    protected $_attribute_collection;
    protected $_attribute_option;    
    
    public function getProduct(){
        $_item = $this->getParentBlock()->getItem();
        return $_item->getProduct();
    }
    
    public function getAttributeOption(){        
        return Mage::helper('configurator')->getAttributeOptionMapping($this->getProduct());
    }
    public function getAttributeCollection(){
        
        $_attribute_option = $this->getAttributeOption();

        $_product = $this->getProduct();          

        $_collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('main_table.attribute_id', array('in'=> array_keys($_attribute_option)));

        $this->_attribute_collection = $_collection;            
        
        return $this->_attribute_collection;
    }
    
    public function getOptionValue($attribute){
        $_attribute_option = $this->getAttributeOption();
        $helper = Mage::helper('configurator');
        return $helper->getAttributeOption($attribute,$_attribute_option[$attribute->getAttributeId()])->getValue();
    }
}