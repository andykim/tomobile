<?php
class MageLynx_Configurator_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action{    
    
    public function addAction(){
        $_predefined_options_json = base64_decode($this->getRequest()->getParam('predefined_options'));
        $_custom_image = base64_decode($this->getRequest()->getParam('custom_image'));                                        
        
        if($_predefined_options_json){
            $_predefined_options = Zend_Json::decode(str_replace("||",'"',$_predefined_options_json));
            
            $_attribute_option = array();
            foreach($_predefined_options as $id => $data){
                if(is_int($id)){
                    $_attribute_option[$id] = $data;
                }
            }
            

            $_initinal_product = Mage::getModel('catalog/product')->load($_predefined_options['id']);
                        
            
            
            $_product = $_initinal_product->duplicate();
            $_product = Mage::getModel('catalog/product')->load($_product->getId());
            
            $product_img_path = Mage::getBaseDir('media').DS.
                            Mage::getModel('catalog/product_media_config')->getBaseMediaPathAddition().DS.
                            'configurator'.DS.
                            basename($_custom_image);                    
            
            $_product->addImageToMediaGallery($product_img_path, 'image');
            
            $_product->setData('sku', $_initinal_product->getSku() ."-". md5($_predefined_options_json));
                        
            
            $_product->setData("configurator_options",$_predefined_options_json);
            $_product->setData("url_key",$_initinal_product->getUrlKey()."-". md5($_predefined_options_json));
            $_product->save();
            
            $this->_forward("edit", "catalog_product", "admin",array('id'=>$_product->getId()));
        }

    }
    
}    