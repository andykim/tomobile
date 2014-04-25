<?php
/*------------------------------------------------------------------------
 # SM Gallery - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Gallery_Model_System_Config_Source_OrderBy
{
	public function toOptionArray()
	{
		return array(
			array('value' => '1',	'label' => Mage::helper('gallery')->__('Time')),
			array('value' => '2', 	'label' => Mage::helper('gallery')->__('Name')),
			array('value' => '3', 		'label' => Mage::helper('gallery')->__('Random')),
		);
	}
}
