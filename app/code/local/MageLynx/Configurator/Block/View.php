<?php

class MageLynx_Configurator_Block_View extends MageLynx_Configurator_Block_Abstract{
    
    protected function _construct(){
        parent::_construct();
        $this->setData('title', Mage::helper('configurator')->__('Customize your %s', Mage::registry('product')->getName()));
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        //attach js/css needed for jQuery accordion
        $this->getLayout()->getBlock("head")->append(
            $this->getLayout()->createBlock("core/text")->setText(
                '<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>'.
                '<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>'
            )
        );                    
    }
    
    protected function _toHtml(){
        if(!$this->getProduct()->getData('configurator_config') 
                && !$this->getProduct()->getData('configurator_attributes')){
            return '';
        }
        return parent::_toHtml();
    }
    
    
}