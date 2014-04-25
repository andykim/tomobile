<?php
class Sm_Total_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function __construct(){
		$this->defaults = array(
			/* general options */
			'showcpanel'				=> '1',
			'color'						=> 'new',
			'body_background_color'		=> '',
			'body_link_color'			=> '',
			'body_text_color'			=> '',
			'header_background_color'	=> '',
			'header_background_image'	=> '',
			'footer_background_color'	=> '',
			'footer_background_image'	=> '1',
			'menu_styles'				=> '1',
			'google_font'				=> 'Oswald',
			'google_font_targets'		=> '',
			'body_font_size'			=> '14px',
			'body_font_family'			=> 'Georgia',
		);
	}

	function get($attributes=array())
	{
		$data = $this->defaults;
		$general 						= Mage::getStoreConfig("total_cfg/general");
		// $module_setting				= Mage::getStoreConfig("all_cfg/setting");
		// $product_selection 			= Mage::getStoreConfig("all_cfg/selection");
		// $product_display_setting 	= Mage::getStoreConfig("all_cfg/display_setting");
		// $advanced 					= Mage::getStoreConfig("all_cfg/advanced");
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		if (is_array($general))					$data = array_merge($data, $general);
		// if (is_array($module_setting)) 			$data = array_merge($data, $module_setting);
		// if (is_array($product_selection)) 		$data = array_merge($data, $product_selection);
		// if (is_array($product_display_setting)) $data = array_merge($data, $product_display_setting);
		// if (is_array($advanced)) 				$data = array_merge($data, $advanced);
		return array_merge($data, $attributes);
	}
}
	 