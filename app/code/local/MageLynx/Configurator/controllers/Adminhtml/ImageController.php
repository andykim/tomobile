<?php
class MageLynx_Configurator_Adminhtml_ImageController  extends Mage_Adminhtml_Controller_Action {            
    protected function _validateFormKey(){        
        return true;
    }
    public function uploadAction(){              
        $helper = Mage::helper('configurator/image');
        $type = $this->getRequest()->getParam('type');
        $size = $this->getRequest()->getParam('size');
        $key = current(array_keys($_FILES));
        $result = $helper->save($type, $key);
        $result['resized_url'] = $helper->resize($result['short_path'],$size);                
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }        
}