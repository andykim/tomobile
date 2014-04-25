<?php
class MageLynx_Configurator_Block_Adminhtml_Layer_Grid_Container extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'configurator';
        $this->_controller = 'adminhtml_layer';
        $this->_headerText = Mage::helper('configurator')->__('Manage Layers');
        $this->_addButtonLabel = Mage::helper('configurator')->__('Add New Layer');
        parent::__construct();
    }

}
