<?php
/*------------------------------------------------------------------------
 # SM total - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.Magentech.com
-------------------------------------------------------------------------*/

class Sm_Total_Model_System_Config_Source_ListMenuStyles
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'1', 'label'=>Mage::helper('total')->__('Mega')),
			array('value'=>'2', 'label'=>Mage::helper('total')->__('Css')),
			array('value'=>'3', 'label'=>Mage::helper('total')->__('Split'))
		);
	}
}
