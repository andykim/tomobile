<?php
/*------------------------------------------------------------------------
 # SM Gallery - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Gallery_Model_System_Config_Source_TitlePosition
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'over',	'label' => Mage::helper('gallery')->__('Over')),
			array('value' => 'outside', 	'label' => Mage::helper('gallery')->__('Outside')),
			array('value' => 'inside', 		'label' => Mage::helper('gallery')->__('Inside')),
		);
	}
}
