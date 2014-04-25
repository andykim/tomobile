<?php
/*------------------------------------------------------------------------
 # SM Gallery - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Gallery_Model_System_Config_Source_TypeTransition
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'none',	'label' => Mage::helper('gallery')->__('None')),
			array('value' => 'elastic', 	'label' => Mage::helper('gallery')->__('Elastic')),
			array('value' => 'fade', 		'label' => Mage::helper('gallery')->__('Fade')),
		);
	}
}
