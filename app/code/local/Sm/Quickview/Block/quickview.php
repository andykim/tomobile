<?php
class Sm_Quickview_Block_Quickview extends Mage_Core_Block_Template {
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getQuickview()     
     { 
        if (!$this->hasData('quickview')) {
            $this->setData('quickview', Mage::registry('quickview'));
        }
        return $this->getData('quickview');
        
    }
}