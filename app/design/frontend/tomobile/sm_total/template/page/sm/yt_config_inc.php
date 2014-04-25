<?php
/*------------------------------------------------------------------------
 * Copyright (C) 2013 The YouTech JSC. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: The YouTech JSC
 * Websites: http://www.magentech.com - http://www.cmsportal.net
-------------------------------------------------------------------------*/
/*--- BEGIN: Theme param config ---*/
$_params = new ThemeParameter();
$_config = array(
	'showcpanel'				=>'1',
	'body_font_size'			=>'14px',
	'body_font_family'			=>'Arial',
	'google_font'				=>'Open Sans',
	'google_font_targets'		=>'#nav > li,.ytc-content-slickslider .content-box .block-title a,ul.yt-tab-navi li a,.yt-col .block .block-title strong,.font1',
	'color'						=>'pink',
	'menu_styles'				=>'1',
	'body_background_color'		=>'#FFFFFF',
	'body_link_color'			=>'#666666',
	'body_text_color'			=>'#444444',
	'header_background_color'	=>'#FFFFFF',
	'header_background_image'	=>'1',	
	'footer_background_color'	=>'#FFFFFF',
	'footer_background_image'	=>'1',
);
if(Mage::getConfig()->getNode('modules/Sm_Total')){
	$_config	=	Mage::helper('total/data')->get();
}
// Logo type
$_params->set('showCpanel',$_config['showcpanel']);// show cpanel
// Logo type
$_params->set('logoType','Text');//image: Image; text: Text
// Logo text desx
$_params->set('logoText','Logo Text');
// Slogan text
$_params->set('sloganText','Slogan'); 
// Fontsize
$_params->set('fontsize',$_config['body_font_size']);//value from 1 to 6
// font family
$_params->set('font_name',$_config['body_font_family']);
// font family Targets
// $_params->set('fonttoTarget',$_config['body_font_family_targets']?$_config['body_font_family_targets']:'.shop-access .links li a, .tags-list li a, .yt-info ul li a,
// #nav > li.active li a, #nav > li.over li a, 
// .sm_megamenu_wrapper_horizontal_menu .sm_megamenu_menu .sm_megamenu_id30 .sm_megamenu_title a span,
// .sm_megamenu_wrapper_horizontal_menu .sm_megamenu_menu .sm_megamenu_id23 .sm_megamenu_title a span,
// .ytc-content-slickslider .content-box .block-content,
// input.quantity-input,
// .price-box,
// .block .block-content,
// .button > span, .form-button > span ,
// .position-2 ,
// .block-tags .actions a span,
// .block-poll .actions .button span,
// .block-subscribe .actions .button span, 
// .font2');
// Google web font
$_params->set('googleWebFont',$_config['google_font']);
// Google WebFont Targets
$_params->set('googleWebFontTargets',$_config['google_font_targets']);
// Theme color
$_params->set('sitestyle',		$_config['color']);//'pink','blue','green','brown','orange','violet';
// Theme menu
$menu_style = array(
	'1'	=>	'mega',
	'2'	=>	'css',
	'3'	=>	'split',
);

$_params->set('menustyle',		$menu_style[$_config['menu_styles']]);//css:CSS Menu; split: Split Menu; mega: Mega Menu
// Body background-color
$_params->set('bgcolor', 		$_config['body_background_color']);
// Body link color
$_params->set('linkcolor', 		$_config['body_link_color']);
// Body text color
$_params->set('textcolor', 		$_config['body_text_color']);
// Header background-color
$_params->set('header-bgcolor', $_config['header_background_color']);
// Header background-image
$_params->set('header-bgimage', 'hpattern'.$_config['header_background_image']);
// Footer background-color
$_params->set('footer-bgcolor', $_config['footer_background_color']);
// Footer background-image
$_params->set('footer-bgimage', 'fpattern'.$_config['footer_background_image']);
// CategoryID array of displays 2nd image when hover - Catalog list
$_params->set('yt_arraydisplaylist', array('4','5','16','17','18'));
/*--- END: Theme param config ---*/


// Global var
global $var_yttheme;
$var_yttheme = new YtTheme('sm_total', $_params);

?>


