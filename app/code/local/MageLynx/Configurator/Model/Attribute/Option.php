<?php

class MageLynx_Configurator_Model_Attribute_Option extends Mage_Core_Model_Abstract {
    protected function _construct(){
        $this->_init('configurator/attribute_option');
    }  
    
    public function getSwatchUrl($sizex = null, $sizey = null){
        $swatch = $this->getSwatch();
        if(!$swatch){
            $swatch = DS.'configurator'.DS."default.png";
        }
        if(is_numeric($sizex)){
            return Mage::helper('configurator/image')->resize($swatch, $sizex, $sizey);
        }else{
            Mage::helper('configurator/image')->getUrl($swatch);
        }
    }
}