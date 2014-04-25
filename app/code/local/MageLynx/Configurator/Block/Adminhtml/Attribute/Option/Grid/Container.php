<?php

class MageLynx_Configurator_Block_Adminhtml_Attribute_Option_Grid_Container extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_blockGroup = 'configurator';
        $this->_controller = 'adminhtml_attribute_option';
        parent::__construct();
        $this->_removeButton("add");                        
    }
}
