<?php

class MageLynx_Configurator_Block_Adminhtml_Layer_Edit_Tab_Extend extends Mage_Adminhtml_Block_Abstract{
    protected function _toHtml(){
        if(Mage::registry('layer')->getId()){
            $layout = Mage::getSingleton('core/layout');
            return $layout->createBlock('configurator/adminhtml_layer_option_grid_container')->getGridHtml();
        }else{
            return Mage::helper('configurator')->__('The newly created options will apper here after saving the layer');
        }
    }
}
