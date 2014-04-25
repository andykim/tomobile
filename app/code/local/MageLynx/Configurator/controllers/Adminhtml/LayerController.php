<?php
class MageLynx_Configurator_Adminhtml_LayerController extends Mage_Adminhtml_Controller_Action{            
    public function optionGridAction(){
        $id = $this->getRequest()->getParam('layer_id');
        $model = Mage::getModel('configurator/layer')->load($id);
        Mage::register('layer', $model);
        
        $this->loadLayout();
        $this->renderLayout();
    }
    
    protected function _initAction(){
        $this->_title($this->__('Configurator'))
             ->_title($this->__('Manage Layers'));
        
        $this->loadLayout()->_setActiveMenu('configurator/layers');
        
        return $this;
    }
    
    public function indexAction(){
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('configurator/adminhtml_layer_grid_container'))
            ->renderLayout();
    }
    
    public function gridAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    
    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('layer_id');
        $model = Mage::getModel('configurator/layer');
        if ($id) {
            $model->load($id);

            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('configurator')->__('This layer no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }
                
        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getLayerData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

        Mage::register('layer', $model);

        $this->_initAction();

        $this->_title($id ? $model->getName() : $this->__('New Layer'));

        $item = $id ? Mage::helper('configurator')->__('Edit Layer')
                    : Mage::helper('configurator')->__('New Layer');

        $this->_addBreadcrumb($item, $item);
        

        $this->renderLayout();

    }
    
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $session = Mage::getSingleton('adminhtml/session');
            $redirectBack   = $this->getRequest()->getParam('back', false);
            
            $model = Mage::getModel('configurator/layer');
            
            $helper = Mage::helper('configurator');

            $id = $this->getRequest()->getParam('layer_id');

            //validate layer_code
            if (isset($data['code'])) {
                $validatorLayerCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorLayerCode->isValid($data['code'])) {
                    $session->addError(
                        $helper->__('Layer code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.'));
                    $this->_redirect('*/*/edit', array('layer_id' => $id, '_current' => true));
                    return;
                }
            }           

            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                        Mage::helper('configurator')->__('This Layer no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }               

                $data['code'] = $model->getCode();                
            }
            
            //filter
            $data = $this->_filterPostData($data);
            $model->addData($data);
                       

            try {
                $model->save();
                $session->addSuccess(
                    Mage::helper('configurator')->__('The Layer has been saved.'));
                
                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setLayerData(false);
                if($redirectBack){
                    $this->_redirect('*/*/edit', array('layer_id' => $model->getId(),'_current'=>true));
                }else{
                    $this->_redirect('*/*/', array());
                }
                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('*/*/edit', array('layer_id' => $id, '_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    protected function _filterPostData($data)
    {
        if ($data) {
            $helperConfigurator = Mage::helper('configurator');

            if (!empty($data['name'])) {
                $data['name'] = $helperConfigurator->escapeHtml($data['name']);
            }            
        }
        return $data;
    }

    public function validateAction()
    {                
        $response = new Varien_Object();
        $response->setError(false);

        $layerCode  = $this->getRequest()->getParam('code');
        $layerId    = $this->getRequest()->getParam('layer_id');
        
        $layer = Mage::getResourceModel('configurator/layer_collection')
                ->addFieldToFilter('code', array('eq' => $layerCode))
                ->getFirstItem();

        if ($layer->getId() && !$layerId) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('configurator')->__('Layer with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('layer_id')) {
            $model = Mage::getModel('configurator/layer');

            $model->load($id);
            
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('configurator')->__('The Layer has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('layer_id' => $this->getRequest()->getParam('layer_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('configurator')->__('Unable to find a layer to delete.'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed(){
        return true;
    }
}    