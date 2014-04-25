<?php
/*------------------------------------------------------------------------
 # SM total - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.Magentech.com
-------------------------------------------------------------------------*/

class Sm_Total_Model_System_Config_Source_ListGoogleFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'Oswald', 'label'=>Mage::helper('total')->__('Oswald')),
			array('value'=>'Open Sans', 'label'=>Mage::helper('total')->__('Open Sans')),
			array('value'=>'BenchNine', 'label'=>Mage::helper('total')->__('BenchNine')),
			array('value'=>'Droid Sans', 'label'=>Mage::helper('total')->__('Droid Sans')),
			array('value'=>'Droid Serif', 'label'=>Mage::helper('total')->__('Droid Serif')),
			array('value'=>'PT Sans', 'label'=>Mage::helper('total')->__('PT Sans')),
			array('value'=>'Vollkorn', 'label'=>Mage::helper('total')->__('Vollkorn')),
			array('value'=>'Ubuntu', 'label'=>Mage::helper('total')->__('Ubuntu')),
			array('value'=>'Neucha', 'label'=>Mage::helper('total')->__('Neucha')),
			array('value'=>'Cuprum', 'label'=>Mage::helper('total')->__('Cuprum'))	
		);
	}
}
