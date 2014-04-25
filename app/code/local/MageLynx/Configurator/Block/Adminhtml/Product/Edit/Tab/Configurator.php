<?php

class MageLynx_Configurator_Block_Adminhtml_Product_Edit_Tab_Configurator extends Mage_Adminhtml_Block_Catalog_Form
{
    
    protected function _prepareForm(){
        $form = new Varien_Data_Form();

        $product = Mage::getModel('catalog/product')->load(Mage::registry('product')->getId());

        $fieldset = $form->addFieldset('group_fields_configurator', array(
            'legend' => Mage::helper('configurator')->__("Settings"),
            'class' => 'fieldset-wide'            
        ));     
        
        $fieldset->addField('configurator_config', 'textarea', array(
            'name'      => 'configurator_config',
            'required'  => false,
            'label'     => Mage::helper('configurator')->__('XML Config'),
            'value'     => $product->getData('configurator_config')
        ));
        
        $fieldset->addField('configurator_attributes', 'multiselect', array(
            'name'      => 'configurator_attributes[]',
            'required'  => false,
            'label'     => Mage::helper('configurator')->__('Configurable attributes'),
            'value'     => explode(",",$product->getData('configurator_attributes')),
            'values'    => Mage::getModel('configurator/source_attribute')->getAllOptions()
        ));
                
        $this->setForm($form);        
    }
}
