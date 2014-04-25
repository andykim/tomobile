<?php
class MageLynx_Configurator_Adminhtml_CacheController extends Mage_Adminhtml_Controller_Action{            
    
    protected function _initAction(){
        $this->_title($this->__('Configurator'))
             ->_title($this->__('Manage Cache'));
        
        $this->loadLayout()->_setActiveMenu('configurator/cache');
        
        return $this;
    }
	
	public function indexAction(){
		$this->_initAction()->renderLayout();
    }
	
	public function postAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
			
			// start clear cache
			$dir = Mage::getBaseDir().'/magelynx_configurator/cache';
			if($this->deleteCache($dir)){
				$message = $this->__('Cache is cleared successfully.');
				Mage::getSingleton('adminhtml/session')->addSuccess($message);
			}else{
				$message = $this->__('Can not clear cache');
				Mage::getSingleton('adminhtml/session')->addError($message);
			}
            
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*');
    }
	
	
	public function deleteCache($dirname){

        if (is_dir($dirname))
           $dir_handle = opendir($dirname);
		  	   
		if (!$dir_handle) return false;
		
		while($file = readdir($dir_handle)) {
		    if ($file != "." && $file != "..") {
				if (!is_dir($dirname."/".$file))
					unlink($dirname."/".$file);
				else
					$this->deleteCache($dirname.'/'.$file);
		    }
		}
		closedir($dir_handle);
		rmdir($dirname);
		
		return true;
	}
}    