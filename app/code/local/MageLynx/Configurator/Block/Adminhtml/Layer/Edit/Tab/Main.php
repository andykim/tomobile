<?php
class MageLynx_Configurator_Block_Adminhtml_Layer_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        parent::_prepareForm();        
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('configurator')->__('Layer Properties'))
        );                                        
        
        $validateClass = sprintf('validate-code validate-length maximum-length-%d',
            MageLynx_Configurator_Model_Layer::LAYER_CODE_MAX_LENGTH);
        
        if(Mage::registry('layer')->getId()){
            $fieldset->addField('layer_id', 'hidden', array(
                'name' => 'layer_id',
            ));
        }
        
        $fieldset->addField('code', 'text', array(
            'name'  => 'code',
            'label' => Mage::helper('configurator')->__('Code'),
            'title' => Mage::helper('configurator')->__('Layer Code'),
            'note'  => Mage::helper('configurator')->__('For internal use. Must be unique with no spaces. Maximum length of layer code must be less then %s symbols',
                MageLynx_Configurator_Model_Layer::LAYER_CODE_MAX_LENGTH),
            'class' => $validateClass,
            'required' => true,
        ));
        
        $fieldset->addField('name', 'text', array(
            'name'  => 'name',
            'label' => Mage::helper('configurator')->__('Name'),
            'title' => Mage::helper('configurator')->__('Layer Name'),
        ), 'code');
        
        
        if (Mage::registry('layer')->getId()) {
            $form->getElement('code')->setDisabled(1);            
        }
        
        
        $this->setForm($form);
        
        return $this;
    }   
    
    protected function _initFormValues(){
        $this->getForm()->addValues(Mage::registry('layer')->getData());
        return parent::_initFormValues();
    }
}
