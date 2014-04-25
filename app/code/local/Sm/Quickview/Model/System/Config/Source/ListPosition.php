<?php
class Sm_Quickview_Model_System_Config_Source_ListPosition {
	public function toOptionArray()
	{
		return array(
				array('value'=>'0', 'label'=>Mage::helper('quickview')->__('top,left')),
				array('value'=>'1', 'label'=>Mage::helper('quickview')->__('top,center')),
				array('value'=>'2', 'label'=>Mage::helper('quickview')->__('top,right')),
				array('value'=>'3', 'label'=>Mage::helper('quickview')->__('center,left')),
				array('value'=>'4', 'label'=>Mage::helper('quickview')->__('center,center')),
				array('value'=>'5', 'label'=>Mage::helper('quickview')->__('center,right')),
				array('value'=>'6', 'label'=>Mage::helper('quickview')->__('bottom,left')),
				array('value'=>'7', 'label'=>Mage::helper('quickview')->__('bottom,center')),
				array('value'=>'8', 'label'=>Mage::helper('quickview')->__('bottom,right')),
		);
	}
}
