<?php
/*------------------------------------------------------------------------
 # SM Block Tabs - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_BlockTabs_Block_Tabs extends Mage_Core_Block_Template
{
	protected $defaultTemplate = 'sm/blocktabs/tabs.phtml';
	public function __construct($attributes = array()){
		parent::__construct($attributes);
		
		$selfData = $this->getData();
		
		// handler configuration for module config
		$configuration = $this->_getConfiguration();
		if ( count($configuration) ){
			foreach ($configuration as $field => $value) {
				//if (!array_key_exists($field, $selfData)){
				$selfData[$field] = $value;
				//}
			}
		}
		
		// handler attributes for {{block ...}} in cms page
		if ( count($attributes) ){
			foreach ($attributes as $field => $value) {
				//if (!array_key_exists($field, $selfData)){
				$selfData[$field] = $value;
				//}
			}
		}
		
		// re-save data
		$this->setData($selfData);
	}
	
	public function setConfig($key, $value = null){
		if ( is_array($key) ){
			foreach ($key as $k => $v){
				$this->setData($k, $v);
			}
		} else {
			$this->setData($key, $value);
		}
	}
	
	protected function _getConfiguration($path = 'blocktabs_cfg'){
		$configuration = Mage::getStoreConfig($path);
		$conf_merged = array();
		foreach( $configuration as $group ){
			foreach($group as $field => $value){
				if (array_key_exists($field, $conf_merged)){
					// no override
				} else {
					$conf_merged[$field] = $value;
				}
			}
		}
		return $conf_merged;
	}
	
	protected function _beforeToHtml(){
		if ( !($template = $this->getTemplate()) ){
			$this->setTemplate($this->defaultTemplate);
		}
		return $this;
	}
}
