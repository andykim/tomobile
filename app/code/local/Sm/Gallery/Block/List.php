<?php
/*------------------------------------------------------------------------
 # SM Gallery - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Gallery_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	protected $_config = null;
	protected $_storeId = null;
	
	public function __construct($attributes = array()){
		parent::__construct();
		$this->_config = Mage::helper('gallery/data')->get($attributes);
	}

	public function getConfig($name=null, $value=null){
		if (is_null($this->_config)){
			$this->_config = Mage::helper('gallery/data')->get(null);
		}
		if (!is_null($name) && !empty($name)){
			$valueRet = isset($this->_config[$name]) ? $this->_config[$name] : $value;
			return $valueRet;
		}
		return $this->_config;
	}
	
	public function setConfig($name, $value=null){
		if (is_null($this->_config)) $this->getConfig();
		if (is_array($name)){
			$this->_config = array_merge($this->_config, $name);
			return;
		}
		if (!empty($name)){
			$this->_config[$name] = $value;
		}
		return true;
	}
	
	protected function _toHtml(){
		if(!$this->_config['isenabled']) return;
		$theme = $this->getConfig('theme');
		$template_file = "sm/gallery/".$theme.".phtml";
		$this->setTemplate($template_file);
		return parent::_toHtml();
	}

	public function getConfigObject(){
		return (object)$this->getConfig();
	}
	
	public function getImages(){
		$path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$images = array();
		if (($this->getConfig('folder')) && file_exists($this->getConfig('folder'))){
			if ($handle = opendir($this->getConfig('folder'))) {
				while (false !== ($file = readdir($handle))) {
					$current_file = $this->getConfig('folder') . '/' . $file;
					if (!is_file($current_file)){
						continue;
					}
					$extension = substr($file, strrpos($file, '.')); // Gets the File Extension
					$extension = strtolower($extension);
					if (!in_array($extension, array(
							'.jpeg',
							'.jpg',
							'.gif',
							'.png'
					))){
						continue;
					}
					$image = array();
					$image['image'] = str_replace('\\', '/', $current_file);
					$filename = basename($current_file, $extension);
					$filename = str_replace('_', ' ', $filename);
					$image['title'] = ucwords($filename);
					$image['modified'] = filemtime($current_file);
					$image['url'] = $path . '/' . str_replace('\\', '/', $current_file);
					$images[] = $image;
				}
				closedir($handle);
			}
			if (count($images)>0){
				// sort image files
				if (($this->getConfig('orderby'))){
					$is_sort_desc = ($this->getConfig('sort'))&&(int)$this->getConfig('sort') ? ((int)$this->getConfig('sort')==2) : false;
					switch ($this->getConfig('orderby')){
						case 1:
							if ($is_sort_desc){
								usort($images, create_function('$a, $b', 'return ($a["modified"] == $b["modified"]) ? strcmp($a["title"], $b["title"]) : ($a["modified"] < $b["modified"]);'));
							} else {
								usort($images, create_function('$a, $b', 'return ($a["modified"] == $b["modified"]) ? strcmp($a["title"], $b["title"]) : ($a["modified"] > $b["modified"]);'));
							}
							break;
						case 2:
							if ($is_sort_desc){
								usort($images, create_function('$a, $b', 'return strcmp($b["title"], $a["title"]);'));
							} else {
								usort($images, create_function('$a, $b', 'return strcmp($a["title"], $b["title"]);'));
							}
							break;
						case 3:
							shuffle($images);
							break;
					}
				}
				// get by limit
				if (($this->getConfig('numberImage')) && (int)$this->getConfig('numberImage')){
					$images = array_slice($images, 0, (int)$this->getConfig('numberImage'));
				}
			}
		}
		return $images;
	}
	
	public function getScriptTags(){
		$import_str = "";
		$jsHelper = Mage::helper('core/js');
		if (null == Mage::registry('jsmart.jquery')){
			// jquery has not added yet
			if (Mage::getStoreConfigFlag('gallery_cfg/advanced/include_jquery')){
				// if module allowed jquery.
				$import_str .= $jsHelper->includeSkinScript('sm/gallery/js/jquery-1.8.2.min.js');
				Mage::register('jsmart.jquery', 1);
			}
		}
		if (null == Mage::registry('jsmart.jquerynoconfict')){
			// add once noConflict
			$import_str .= $jsHelper->includeSkinScript('sm/gallery/js/jquery-noconflict.js');
			Mage::register('jsmart.jquerynoconfict', 1);
		}
		if (null == Mage::registry('jsmart.gallery.js')){
			// add script for this module.
			$import_str .= $jsHelper->includeSkinScript('sm/gallery/js/jsmart.easing.1.3.js');
			$import_str .= $jsHelper->includeSkinScript('sm/gallery/js/jquery.fancybox-1.3.4.pack.js');
			$import_str .= $jsHelper->includeSkinScript('sm/gallery/js/jcarousel.js');
			Mage::register('jsmart.gallery.js', 1);
		}
		return $import_str;
	}
}
