<?php
class MageLynx_Configurator_Adminhtml_AttributeController extends Mage_Adminhtml_Controller_Action{            
    
    public function optionGridAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function attributeSaveAction(){
        $data = $this->getRequest()->getPost();
        $add_model = Mage::getModel('configurator/attribute')
                    ->setData($data)
                    ->save();
    }
}    