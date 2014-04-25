<?php
/*------------------------------------------------------------------------
 # SM Base News - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Basenews_Model_System_Config_Source_LinkTargets
{
	public function toOptionArray()
	{
		return array(
			array('value'=>'_self',	'label'=>Mage::helper('basenews')->__('Same Window')),
        	array('value'=>'_blank','label'=>Mage::helper('basenews')->__('New Window')),
			array('value'=>'_popup','label'=>Mage::helper('basenews')->__('Popup Window'))
		);
	}
}
