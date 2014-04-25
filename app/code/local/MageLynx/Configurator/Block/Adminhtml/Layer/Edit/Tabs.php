<?php
class MageLynx_Configurator_Block_Adminhtml_Layer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('layer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('configurator')->__('Layer Information'));
    }

    
    protected function _beforeToHtml(){
        $this->addTab('main', array(
            'label'     => Mage::helper('configurator')->__('Properties'),
            'title'     => Mage::helper('configurator')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('configurator/adminhtml_layer_edit_tab_main')->toHtml(),
            'active'    => true
        ));
               
        $this->addTab('Options', array(
            'label'     => Mage::helper('configurator')->__('Add Options'),
            'title'     => Mage::helper('configurator')->__('Add Options'),
            'content'   => $this->getLayout()->createBlock('configurator/adminhtml_layer_edit_tab_options')->toHtml(),
        ));
        
        $this->addTab('Extend Options', array(
            'label'     => Mage::helper('configurator')->__('Extend Options'),
            'title'     => Mage::helper('configurator')->__('Extend Options'),
            'content'   => $this->getLayout()->createBlock('configurator/adminhtml_layer_edit_tab_extend')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
