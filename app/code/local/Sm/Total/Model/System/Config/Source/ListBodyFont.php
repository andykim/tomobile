<?php
/*------------------------------------------------------------------------
 # SM total - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.Magentech.com
-------------------------------------------------------------------------*/

class Sm_Total_Model_System_Config_Source_ListBodyFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'Arial', 'label'=>Mage::helper('total')->__('arial')),
			array('value'=>'Arial Black', 'label'=>Mage::helper('total')->__('arial-black')),
			array('value'=>'Courier New', 'label'=>Mage::helper('total')->__('courier')),
			array('value'=>'Georgia', 'label'=>Mage::helper('total')->__('georgia')),
			array('value'=>'Impact', 'label'=>Mage::helper('total')->__('impact')),
			array('value'=>'Lucida Console', 'label'=>Mage::helper('total')->__('lucida-console')),
			array('value'=>'Lucida Grande', 'label'=>Mage::helper('total')->__('lucida-grande')),
			array('value'=>'Palatino', 'label'=>Mage::helper('total')->__('palatino')),
			array('value'=>'Tahoma', 'label'=>Mage::helper('total')->__('tahoma')),
			array('value'=>'Times New Roman', 'label'=>Mage::helper('total')->__('times')),	
			array('value'=>'Trebuchet', 'label'=>Mage::helper('total')->__('trebuchet')),	
			array('value'=>'Verdana', 'label'=>Mage::helper('total')->__('verdana'))		
		);
	}
}
