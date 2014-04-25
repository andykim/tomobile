<?php

class MageLynx_Configurator_Block_Adminhtml_Sales_Order_Item_Column_Options extends Mage_Adminhtml_Block_Sales_Items_Column_Default{    
    protected $_attribute_collection;
    protected $_attribute_option;
    protected $_options;
    
    public function getPredefinedJson(){
        $_predefined_options_json = false;
        
        foreach($this->getCustomOptions() as $_option){
            if($_option->getCode()=="predefined_options"){
                $_predefined_options_json = $_option->getValue();
            }
        }
        
        return $_predefined_options_json;
    }
    public function getCustomImage(){
        $_custom_image = false;
        
        foreach($this->getCustomOptions() as $_option){
            if($_option->getCode()=="custom_image"){
                $_custom_image = $_option->getValue();
            }
        }
        
        return $_custom_image;       
    }
    
    
    public function getCustomOptions(){        
        
        $_item = $this->getItem();

        $options = Mage::getResourceModel('sales/quote_item_option_collection')
                ->addFieldToFilter('item_id', array('eq'=>$_item->getQuoteItemId()))
                ->addFieldToFilter('code', array('in'=>array("predefined_options","custom_image")))
                ->getItems();
        $this->_options = $options;
        
        return $this->_options;
    }
    
    public function getAttributeOption(){
        
        if(!isset($this->_attribute_option)){
            $_attribute_option = array();
            if($_predefined_options_json = $this->getPredefinedJson()){
                $_predefined_options = Zend_Json::decode(str_replace("||",'"',$_predefined_options_json));
                foreach($_predefined_options as $id => $data){
                    if(is_int($id)){
                        if($id == Mage::getStoreConfig('configurator/settings/color')) continue;
                        $_attribute_option[$id] = $data;
                    }
                }
            }
            $this->_attribute_option = $_attribute_option;
        }        
        return $this->_attribute_option;
    }
    
    public function getAttributeCollection(){
        if(!isset($this->_attribute_collection)){
            $_attribute_option = $this->getAttributeOption();
        
            $_collection = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->addFieldToFilter('main_table.attribute_id', array('in'=> array_keys($_attribute_option)));
            $this->_attribute_collection = $_collection;
        }                
        return $this->_attribute_collection;
    }    
    
    public function getOptionValue($attribute){
        $_attribute_option = $this->getAttributeOption();
        $option_id = $_attribute_option[$attribute->getAttributeId()];
        return Mage::helper('configurator')->getAttributeOption($attribute, $option_id)->getValue();
    }
    
    public function getProductImgPath(){
        if($_custom_image = $this->getCustomImage()){
            $product_img_path = Mage::getBaseDir('media').DS.
                            Mage::getModel('catalog/product_media_config')->getBaseMediaPathAddition().DS.
                            'configurator'.DS.
                            basename($_custom_image);
            
            return $product_img_path;
        }
        return "";
    }
    
    public function getImageUrl(){
        return str_replace(Mage::getBaseDir('media'), Mage::getBaseUrl('media'),$this->getProductImgPath());
    }
}

