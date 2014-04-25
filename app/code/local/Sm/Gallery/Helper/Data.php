<?php
/*------------------------------------------------------------------------
 # SM Gallery - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Gallery_Helper_Data extends Mage_Core_Helper_Abstract {
	public function __construct(){
		$this->defaults = array(
			/* General setting */
			'isenabled'		=> '1',
			'title' 		=> 'SM Gallery',
			/* Module setting */
			'theme'        => 'default',
			'folder' 		=> '',
			'numberImage' 			=> '27',			
			'items_page'	=> '9',
			'orderby'=> '1',
			'sort'	=> '1',
			'titleposition'	=> 'over',
			'transition'	=> 'elastic',
			'show_nextprev'=> '1',
			'play'	=> '1',
			'interval'=> '5000',
			'effect' 		=> 'slide',
			/*'product_image_width'		=> '300',
			'product_image_height' 	    => '200',*/
			/* Advanced options */
			'deviceclass_sfx'	=> 'preset01-4 preset02-3 preset03-2 preset04-1 preset05-1',
			'include_jquery'	=> '1',
			'pretext'			=> '',
			'posttext'			=> ''
		);
	}

	function get($attributes=array())
	{
		$data = $this->defaults;
		$general 					= Mage::getStoreConfig("gallery_cfg/general");
		$module_setting				= Mage::getStoreConfig("gallery_cfg/module_setting");
		$advanced 					= Mage::getStoreConfig("gallery_cfg/advanced");
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		if (is_array($general))					$data = array_merge($data, $general);
		if (is_array($module_setting)) 			$data = array_merge($data, $module_setting);
		if (is_array($advanced)) 				$data = array_merge($data, $advanced);
		
		return array_merge($data, $attributes);;
	}
}
?>