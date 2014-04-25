<?php
/*------------------------------------------------------------------------
 # SM total - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.Magentech.com
-------------------------------------------------------------------------*/

class Sm_Gallery_Model_System_Config_Source_ListTheme
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'default', 'label'=>Mage::helper('gallery')->__('Layout 01')),
			array('value'=>'brand', 'label'=>Mage::helper('gallery')->__('Layout 02')),
		);
	}
}
