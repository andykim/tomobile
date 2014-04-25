<?php
/*------------------------------------------------------------------------
 # SM Block Tabs - Version 1.0
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_BlockTabs_Block_Tabs_Featured extends Sm_BasicProducts_Block_Home
{
	
	protected function _getConfiguration($path = 'blocktabs_cfg'){
		return parent::_getConfiguration($path);
	}
	
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			$collection = Mage::getSingleton('catalog/product')->getCollection();
			$collection->addAttributeToSelect('*');
			$collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
			$visibility = array(
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
			);
			
			$collection->addAttributeToFilter('visibility', $visibility);
			
			$this->isExistAttribute('is_featured') && $collection->addAttributeToFilter('is_featured', 1);
				
			$collection->addStoreFilter();
			
			$numProducts = $this->getProductLimitation()>0 ? intval($this->product_limitation) : 0;
			$collection->setPage(1, $numProducts);
				
			$this->_productCollection = $collection;
		}
		return $this->_productCollection;
	}
	protected function isExistAttribute($code){
		$entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
		$model = Mage::getModel('catalog/resource_eav_attribute');
		$model->loadByCode($entityTypeId, $code);
		return $model->getName();
	}
}
