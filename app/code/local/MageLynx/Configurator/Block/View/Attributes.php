<?php

class MageLynx_Configurator_Block_View_Attributes extends MageLynx_Configurator_Block_Abstract{                
                       
    
    public function getColorAttribute(){
        return Mage::helper('configurator')->getColorAttribute();
    }
    
    public function renderAttribute($attribute){
        if(Mage::getStoreConfig('configurator/settings/color') != $attribute->getAttributeId()){//color attribute rendered separately           
            return Mage::getModel('core/layout')->createBlock('configurator/view_attributes_item_renderer',null,array(
                        'attribute' => $attribute
                    ))
                    ->toHtml();
        }
        return '';
    }
    
    public function renderColorAttribute(){
        if($this->getColorAttribute()){
                    
        return Mage::getModel('core/layout')->createBlock('configurator/view_attributes_item_renderer',null,array(
                        'attribute' => $this->getColorAttribute()
                    ))
                    ->toHtml();
        }
        return '';
    }
    
    
}