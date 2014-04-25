<?php
/*------------------------------------------------------------------------
 # SM total - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.Magentech.com
-------------------------------------------------------------------------*/

class Sm_Total_Model_System_Config_Source_ListColor
{
	public function toOptionArray()
	{	
		return array(
                        array('value'=>'new', 'label'=>Mage::helper('total')->__('New')),
			array('value'=>'pink', 'label'=>Mage::helper('total')->__('Pink')),
			array('value'=>'green', 'label'=>Mage::helper('total')->__('Green')),
			array('value'=>'blue', 'label'=>Mage::helper('total')->__('Blue')),
			array('value'=>'brown', 'label'=>Mage::helper('total')->__('Brown')),
			array('value'=>'orange', 'label'=>Mage::helper('total')->__('Orange')),				
			array('value'=>'violet', 'label'=>Mage::helper('total')->__('Violet'))
		);
	}
}
