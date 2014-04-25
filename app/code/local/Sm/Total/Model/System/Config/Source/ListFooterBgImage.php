<?php
/*------------------------------------------------------------------------
 # SM total - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.Magentech.com
-------------------------------------------------------------------------*/

class Sm_Total_Model_System_Config_Source_ListFooterBgImage
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'1', 'label'=>Mage::helper('total')->__('Fpattern1')),
			array('value'=>'2', 'label'=>Mage::helper('total')->__('Fpattern2')),
			array('value'=>'3', 'label'=>Mage::helper('total')->__('Fpattern3')),
			array('value'=>'4', 'label'=>Mage::helper('total')->__('Fpattern4')),
			array('value'=>'5', 'label'=>Mage::helper('total')->__('Fpattern5'))
		);
	}
}
