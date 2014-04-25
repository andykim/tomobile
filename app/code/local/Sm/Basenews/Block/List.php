<?php
/*------------------------------------------------------------------------
 # SM Base News - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Basenews_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	protected $_config = null;
	protected $_storeId = null;
	protected $_productCollection = null;

	public function __construct($attributes = array()){
		parent::__construct();
		$this->_config = Mage::helper('basenews/data')->get($attributes);
	}

	public function getConfig($name=null, $value=null){
		if (is_null($this->_config)){
			$this->_config = Mage::helper('basenews/data')->get(null);
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

	public function getConfigObject(){
		return (object)$this->getConfig();
	}

	protected function _toHtml(){
		/*$template = $this->getConfig('theme', 'theme1');
		$template_file = "sm/basenews/default.phtml";
		$this->setTemplate($template_file);*/
		return parent::_toHtml();
	}

	public function getCmspageInfo(){
		$list = array();
		$page_url = preg_split ("/\n/", $this->getConfig('cms_page'));
		$items =array();
		while(count($page_url)){
			$item = array();
			$item= trim(array_shift($page_url));
			array_push($items, $item);
		}
		if( !empty($items) ){
			foreach( $items as $page ){
				$page_obj = new stdClass();
				$page_content = Mage::getModel('cms/page')->load($page,'identifier');
				$page_obj->id = $page_content->getId();

				//ago days
				$creation_time = strtotime(substr($page_content->getCreationTime(),0,10));
     			$now = strtotime(date('Y-m-d'));
     			$ago_days = floor(abs($now - $creation_time)/(60*60*24));
				$page_obj->ago_days = $ago_days;	
				// create time
				$page_obj->creation_time = date('d',strtotime($page_content->getCreationTime()));


				$page_obj->full_title = $page_content->getTitle();
				if ( ($maxchars = $this->getConfig('product_title_max_characters',-1))>0 ){
					$page_obj->title = Mage::helper('basenews/data')->truncate($page_obj->full_title, $maxchars, '');
				} else {
					$page_obj->title = $page_obj->full_title;
				}
				$page_obj->link = Mage::Helper('cms/page')->getPageUrl($page_content->getId());
				$page_obj->description = $page_content->getContent();
				if ( (int)$this->getConfig('item_description_striptags') == 1 ){
					$keep_tags = $this->getConfig('item_description_keeptags', '');
					$keep_tags = str_replace(array(' '), array(''), $keep_tags);
					$tmp_desc = strip_tags($page_obj->description ,$keep_tags );
					$page_obj->desc = $tmp_desc;
				} else {
					$page_obj->desc = $page_obj->description;
				}
				if (($maxchars = $this->getConfig('item_desc_max_characs',-1))>0){
					$page_obj->desc =  Mage::helper('basenews/data')->truncate($page_obj->desc, $maxchars, '');
				}
				$media = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
				$image = $this->getImage($page_obj->description);
				if($image != ""){
					$page_obj->image = $media."".$this->getImage($page_obj->description);
				}else{
					$page_obj->image = "";
				}
				if( $page_obj->id != "" ){
					$list[$page_obj->id] = $page_obj;
				}

			}
		}
		return $list;
	}

	public function getImage($text){
		preg_match_all("/<img .*?(?=url)url=\"([^\"]+)\"/si", $text, $m);
		$img = $m[1][0];
		return $img;
	}

	public function getScriptTags(){
		$import_str = "";
		$jsHelper = Mage::helper('core/js');
		if (null == Mage::registry('jsmart.jquery')){
			// jquery has not added yet
			if (Mage::getStoreConfigFlag('basenews_cfg/advanced/include_jquery')){
				// if module allowed jquery.
				$import_str .= $jsHelper->includeSkinScript('sm/basenews/js/jquery-1.8.2.min.js');
				Mage::register('jsmart.jquery', 1);
			}
		}
		if (null == Mage::registry('jsmart.jquerynoconfict')){
			// add once noConflict
			$import_str .= $jsHelper->includeSkinScript('sm/basenews/js/jquery-noconflict.js');
			Mage::register('jsmart.jquerynoconfict', 1);
		}

		if (null == Mage::registry('jsmart.basenews.js')){
			// add script for this module.
			$import_str .= $jsHelper->includeSkinScript('sm/basenews/js/jquery.sm_accordion.js');
			Mage::register('jsmart.basenews.js', 1);
		}
		return $import_str;
	}

}
