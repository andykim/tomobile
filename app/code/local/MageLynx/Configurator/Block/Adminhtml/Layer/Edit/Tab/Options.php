<?php

class MageLynx_Configurator_Block_Adminhtml_Layer_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget{
    public function __construct(){
        parent::__construct();
        $this->setTemplate('magelynx/configurator/layer/options.phtml');
    }
    
    protected function _prepareLayout(){
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('configurator')->__('Delete'),
                    'class' => 'delete delete-option'
                )));

        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('configurator')->__('Add Option'),
                    'class' => 'add',
                    'id'    => 'add_new_option_button'
                )));
        return parent::_prepareLayout();
    }

    public function getDeleteButtonHtml(){
        return $this->getChildHtml('delete_button');
    }

    public function getAddNewButtonHtml(){
        return $this->getChildHtml('add_button');
    }
    
    public function getOptionValues(){
        $result = array();
        if(Mage::registry('layer')->getId()){            
            $options = Mage::registry('layer')->getOptionCollection()->getItems();
            foreach($options as $option){
                $result[] = array('id' => $option->getId(), 'name'=>$option->getName());
            }
        }
        return $result;
    }
}
