<?php

class MageLynx_Configurator_Block_View_Attributes_Item_Renderer extends MageLynx_Configurator_Block_View_Attributes{            
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('magelynx/configurator/view/attributes/item/renderer.phtml');
    }
    
    public function getOptionCollection($attribute = null){
        return Mage::helper('configurator')->getOptionCollection($this->getAttribute());
    }      
    
    public function isActive($option){
        return $this->getPredefinedData()->getData($this->getAttribute()->getAttributeId())==$option->getOptionId();
    }
    
    public function isColorAttribute(){
        return $this->getAttribute()->getAttributeId() == Mage::getStoreConfig('configurator/settings/color');
    }
}