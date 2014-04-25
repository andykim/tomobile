<?php
class MageLynx_Configurator_Block_Adminhtml_Layer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'layer_id';
        $this->_blockGroup = 'configurator';
        $this->_controller = 'adminhtml_layer';

        parent::__construct();

        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('configurator')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ),
            100
        );

        $this->_updateButton('save', 'label', Mage::helper('configurator')->__('Save Layer'));
        $this->_updateButton('save', 'onclick', 'saveLayer()');

        if (! Mage::registry('layer')) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('configurator')->__('Delete Layer'));
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('layer')->getId()) {
            $name = Mage::registry('layer')->getName();            
            return Mage::helper('configurator')->__('Edit Layer "%s"', $this->htmlEscape($name));
        }
        else {
            return Mage::helper('configurator')->__('New Layer');
        }
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/'.$this->_controller.'/save', array('_current'=>true, 'back'=>null));
    }
}
