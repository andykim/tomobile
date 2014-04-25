<?php

class MageLynx_Configurator_Model_Observer{
    public function adminAttributePrepareForm($event){
        $form = $event->getForm();
        
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        $_attribute_id = Mage::app()->getFrontController()->getRequest()->getParam("attribute_id");
        
        $fieldset = $form->addFieldset('configurator_fieldset', 
                array('legend'=>Mage::helper('configurator')->__('Configurator options')),
                'base_fieldset');
        
        $_attribute = Mage::getModel('eav/entity_attribute')->load($_attribute_id);
        
        
        $attribute_data = Mage::getModel('configurator/attribute')->load($_attribute_id);
        
        $is_new_attribute = Mage::app()->getFrontController()->getRequest()->getParam("new");                    
        
        
        if(!$is_new_attribute && $_attribute->getData('frontend_input') == 'select' ){
            $fieldset->addField('note', 'note', array(
                'name'     => 'note',                
                'text'   => Mage::helper('configurator')->__('Click on tab "Extended options" in order to manage Configurator settings')
            ));            
            
            $fieldset->addField('inventory_enabled', 'select', array(
                'name'     => 'inventory_enabled',
                'label'    => Mage::helper('configurator')->__('Is inventory enabled'),            
                'value'    => $attribute_data->getData('inventory_enabled'),
                'values'   => $yesnoSource
            ));

            $fieldset->addField('pricing_enabled', 'select', array(
                'name'     => 'pricing_enabled',
                'label'    => Mage::helper('configurator')->__('Is pricing enabled'),
                'value'    => $attribute_data->getData('pricing_enabled'),
                'values'   => $yesnoSource
            ));                       
            
            $js = '';
            $js .= ' <button type="button" onclick="optionsGridColumnsApply();return;"><span><span>'.Mage::helper('configurator')->__('Ok').'</span></span></button>';
            $js .= '<script type="text/javascript">';          
            $values = " inventory_enabled: $('inventory_enabled').value, pricing_enabled: $('pricing_enabled').value, attribute_id : $_attribute_id ";
            $js .= ' function optionsGridColumnsApply(){ ';
            $js .= " new Ajax.Request('".Mage::getUrl('configurator/adminhtml_attribute/attributeSave')."', { method: 'post', parameters: { $values }, onSuccess: function(){";
            $js .= " optionGridJsObject.reload('".Mage::getUrl('configurator/adminhtml_attribute/optionGrid', array('attribute_id' => $_attribute_id))."') ";
            $js .= " } }); ";            
            $js .= ' } ';
            $js .= ' </script>';
            $fieldset->addField('configurator_update', 'label', array(
                'name'     => 'configurator_update',                
                'value'     => Mage::helper('configurator')->__('Update options grid'),
                'after_element_html' => $js
            ));                       
        }else{
            $fieldset->addField('note', 'note', array(
                'name'     => 'note',                
                'text'   => Mage::helper('configurator')->__('Extended fields are available for options with parameter "Catalog Input Type for Store Owner" set to "Dropdown"')
            ));            
        }        
    }
    public function adminBlockToHtmlBefore($event){
        $block = $event->getBlock();
        
        if($block instanceof Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs){                
            
            $_attribute_id = Mage::app()->getFrontController()->getRequest()->getParam("attribute_id");            
            $_attribute = Mage::getModel('eav/entity_attribute')->load($_attribute_id);            
            $is_new_attribute = Mage::app()->getFrontController()->getRequest()->getParam("new");            
            if(!$is_new_attribute && $_attribute->getData('frontend_input') == 'select'){
                // add extended options tab            
            
                $layout = Mage::getSingleton('core/layout');
                $html = $layout->createBlock('configurator/adminhtml_attribute_option_grid_container')->getGridHtml();

                $block->addTabAfter('configurator', array(
                        'label'     => Mage::helper('configurator')->__('Extended Options'),
                        'title'     => Mage::helper('configurator')->__('Extended Options'),
                        'content'   => $html,
                    ),'labels');            
            }            
        }elseif($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs){
            
            if(Mage::registry('product')->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE){
                $layout = Mage::getSingleton('core/layout');
                $block->addTab('group_configurator', array(
                        'label'     => Mage::helper('configurator')->__('Configurator'),
                        'content'   => $layout->createBlock('configurator/adminhtml_product_edit_tab_configurator',
                            'adminhtml.catalog.product.edit.tab.configurator')->toHtml(),
                    ));
            }                        
        }
    }
    public function modelSaveAfter($event){
        $object = $event->getObject();        
        if($object instanceof Mage_Catalog_Model_Resource_Eav_Attribute){           
            $attribute_id = $object->getAttributeId();
            
            $data = Mage::app()->getFrontController()->getRequest()->getParams();
            //additional fields:
            $add_model = Mage::getModel('configurator/attribute')
                    ->setData($data)
                    ->setAttributeId($attribute_id)                    
                    ->save();                        
            
            //insert all newly created options            
            $_original_option_ids = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attribute_id)
                    ->getColumnValues('option_id');
            
            $_configurator_option_ids = Mage::getResourceModel('configurator/attribute_option_collection')
                    ->addFieldToFilter('configurator.attribute_id', array('eq'=>$attribute_id))
                    ->getColumnValues('option_id');
            
            $_new_option_ids = array_diff($_original_option_ids, $_configurator_option_ids);
            
            foreach($_new_option_ids as $option_id){
                Mage::getModel('configurator/attribute_option')
                        ->setAttributeId($object->getAttributeId())
                        ->setId($option_id)
                        ->save();
            }
            
            //update configurator options data                                                                                    
            
            $fields = array_keys(Mage::getResourceModel('configurator/attribute_option')->getJoinFields());
            
            foreach($fields as $field){
                if(isset($data[$field]) && is_array($data[$field])){
                    foreach($data[$field] as $option_id => $value){
                        if(in_array($option_id,$_original_option_ids)){
                            $model = Mage::getModel('configurator/attribute_option')
                                ->setAttributeId($attribute_id)
                                ->setId($option_id)
                                ->setData($field,$value)
                                ->save();
                        }
                        
                    }
                }
            }                    
        }elseif($object instanceof Mage_Catalog_Model_Product){
            $data = Mage::app()->getFrontController()->getRequest()->getParams();
            if(isset($data['configurator_config'])){
                $attrData = array(
                    'configurator_config' => $data['configurator_config'],
                    'configurator_attributes' => join(",",$data['configurator_attributes'])
                );
                Mage::getModel('catalog/product_action')->updateAttributes(array($object->getId()),$attrData,0);
            }            
        }
    }
    
    public function frontendBlockToHtmlBefore($event){
        $block = $event->getBlock();
        
        if($block instanceof Mage_Checkout_Block_Cart_Item_Renderer){
            //custom image fix:
            $_product = $block->getProduct();
            if($custom_image = $block->getProduct()->getCustomOption('custom_image')){
                $custom_image = $custom_image->getValue();
                $destination_dir = Mage::getBaseDir('media').DS.
                        Mage::getModel('catalog/product_media_config')->getBaseMediaPathAddition().DS.
                        'configurator'.DS;
                $product_img_path = $destination_dir.basename($custom_image);                
                if(!is_file($product_img_path)){
                    $ioAdapter = new Varien_Io_File();                    
                    $ioAdapter->setAllowCreateFolders(true);
                    $ioAdapter->open(array(
                        'path'=>$destination_dir
                    ));
                    $return=$ioAdapter->cp($custom_image,$product_img_path);
                }
                $thumbnail = Mage::helper('catalog/image')->init($_product,'thumbnail','configurator'.DS.basename($custom_image));
                $block->overrideProductThumbnail($thumbnail);
            }
        }
    }    
    
    public function prepareProductOptions($event){
        $predefined_options = $event->getBuyRequest()->getPredefinedOptions();
        $custom_image = $event->getBuyRequest()->getCustomImage();
        
        $product = $event->getProduct();
        
        
        if($predefined_options){
            $product->addCustomOption('predefined_options',$predefined_options);
        }
        if($custom_image){
            $custom_image = str_replace(Mage::getBaseUrl('media'), Mage::getBaseDir('media').DS, $custom_image);
            $custom_image = str_replace("/", DS, $custom_image);
            $product->addCustomOption('custom_image',$custom_image);
            $product->setThumbnail($custom_image);
        }        
    }    
    
    private function _updatePrice($item){       
        $product = $item->getProduct();        
        
        $price = 0;
        $helper = Mage::helper('configurator');
        foreach($helper->getAttributeOptionMapping($product) as $_attribute_id => $option_id){
            $_attribute_add = Mage::getModel('configurator/attribute')->load($_attribute_id);
            $_option = $helper->getAttributeOption($_attribute_id, $option_id);            
            if($_attribute_add->getData('pricing_enabled')){
                $price+= $_option->getData('price');// * $item->getData('qty');
            }                        
        }
        
        if($price){
            $new_price = $product->getFinalPrice()+ $price;
            $item->setCustomPrice($new_price);
            $item->setOriginalCustomPrice($new_price);
            $product->setIsSuperMode(true);
        }        
        
    }    
    
    public function cartSaveBefore($event){
        $cart = $event->getCart();
        $items = $cart->getItems();
        foreach($items as $item){
            $this->_updatePrice($item);
        }        
    }
    
    public function orderPlaceAfter($event){
        $order = $event->getOrder();        
        $items = $order->getAllItems();
        $helper = Mage::helper('configurator');
        
        foreach($items as $item){
            $product = $item->getProduct();
            foreach($helper->getAttributeOptionMapping($product) as $_attribute_id => $option_id){
                $_attribute_add = Mage::getModel('configurator/attribute')->load($_attribute_id);
                $_option = $helper->getAttributeOption($_attribute_id, $option_id);        
                if($_attribute_add->getData('inventory_enabled')){                    
                    $_option->setQty($_option->getQty() - $item->getQtyOrdered());                    
                    $_option->save();
                }                
            }
        }
    }
            
}