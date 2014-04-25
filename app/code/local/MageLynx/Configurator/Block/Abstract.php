<?php

abstract class MageLynx_Configurator_Block_Abstract extends Mage_Core_Block_Template{
    private $_predefined_data;
    public function getOptionCollection($attribute){
        return Mage::helper('configurator')->getOptionCollection($attribute);
    }
    
    public function getProduct(){
        return Mage::registry('current_product');
    }    
    
    public function useDynamicPreview(){        
        if(Mage::helper('configurator')->parsePreviewConfig($this->getProduct())){
            return 1;
        }        
        return 0;
    }
    
    public function __construct() {
        parent::__construct();
        
        $attributes_ids = explode("," , Mage::registry('product')->getConfiguratorAttributes());
        
        $_attribute_collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('main_table.`attribute_id`', array('in'=>$attributes_ids));
        
        $this->setAttributeCollection($_attribute_collection);
    }
    
    public function getJsonOptionsConfig(){
        $attributes = $this->getAttributeCollection()->getItems();
        
        $options_config = array();        

        foreach($attributes as $attribute){
            $option_collection = $this->getOptionCollection($attribute);
            
            $options_config[$attribute->getAttributeId()]['items'] = array();
            foreach ($option_collection as $item) {
                $options_config[$attribute->getAttributeId()]['items'][$item->getId()] = $item->toArray();
            }
            $options_config[$attribute->getAttributeId()]['label'] = $attribute->getFrontendLabel();
            $options_config[$attribute->getAttributeId()]['config'] = Mage::getModel('configurator/attribute')->load($attribute->getAttributeId())->getData();
        }
        
        return Zend_Json::encode($options_config);
    }
    
    public function getProductPredefinedString(){
        if(1/** @todo  condition - is configuring cart item - checkout/cart/configure/id/{product_id}*/){
            $id = (int) $this->getRequest()->getParam('id');
            $quoteItem = null;
            $cart = Mage::getSingleton('checkout/cart');
            if ($id) {
                $quoteItem = $cart->getQuote()->getItemById($id);                    
                if($quoteItem && $quoteItem->getProduct()->getCustomOption('predefined_options')){
                    $source = $quoteItem->getProduct()->getCustomOption('predefined_options')->getValue();
                }
            }

        }
        
        if(empty($source)){                
            $source = $this->getProduct()->getData("configurator_options");
        }
            
        return $source;
    }
    
    public function getPredefinedData(){        
        if(!isset($this->_predefined_data)){
                    
            
            $source = $this->getProductPredefinedString();
            
            $configurator_data = Zend_Json::decode(str_replace("||",'"',$source));
            if(!is_array($configurator_data)){
                $configurator_data = array();
            }                
            $this->_predefined_data = new Varien_Object($configurator_data);
        }
        return $this->_predefined_data;
    }
    
    public function getUpdaterUrl(){
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'magelynx_configurator/update.php';
    }    
    
    public function getColorPickerUrl(){
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'magelynx_configurator/getcoloratpixel.php';
    }
    
    public function getJsonPreviewData(){
        $return = '{}';
        if($this->useDynamicPreview()){            
            
            $options = $this->getPredefinedData()->toArray();
            $options['id'] = $this->getProduct()->getId();            
            
            $_options = array();
            foreach($options as $key => $option){
                if(is_numeric($key)){
                    $_options['numeric_'.$key] = $option;
                }
                else{
                    $_options[$key] = $option;
                }
            }
            
            $curl = curl_init(); 
            curl_setopt($curl, CURLOPT_URL, $this->getUpdaterUrl()); 
            
            curl_setopt($curl, CURLOPT_POST, 1);             
            curl_setopt($curl, CURLOPT_POSTFIELDS, $_options); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            
            $return = curl_exec($curl);  
        }
        return $return;
    }
    
    public function getColorsData(){
        return $this->getPredefinedData()->getColorsData();
    }
}