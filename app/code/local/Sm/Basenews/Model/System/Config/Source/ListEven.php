<?php
/*------------------------------------------------------------------------
 # SM Base News - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Basenews_Model_System_Config_Source_ListEven
{
	public function toOptionArray()
	{
		return array(
		array('value'=>'click', 'label'=>Mage::helper('basenews')->__('Click')),
		array('value'=>'mouseenter', 'label'=>Mage::helper('basenews')->__('Hover')),
		);
	}
}
