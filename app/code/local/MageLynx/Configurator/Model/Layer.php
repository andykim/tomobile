<?php

class MageLynx_Configurator_Model_Layer extends Mage_Core_Model_Abstract {
    CONST LAYER_CODE_MAX_LENGTH                 = 30;
    
    protected function _construct(){
        $this->_init('configurator/layer');
    }  
    
    public function getOptionCollection(){
        $collection = Mage::getResourceModel('configurator/layer_option_collection');
        if($this->getId()){
            $collection->addFieldToFilter('layer_id', array('eq'=>$this->getId()));
        }
        
        return $collection;                
    }
    
    public function loadByCode($code){
        return $this->getCollection()
                ->addFieldToFilter('code', array('eq' => $code))
                ->getFirstItem();
    }
    
    protected function _afterSave(){
        $data = Mage::app()->getFrontController()->getRequest()->getParams();
        
        //apply adding/deleting options
        if(isset($data['option'])){
            $option = $data['option'];                        
            
            foreach($option['name'] as $option_id => $option_name){
                $model = Mage::getModel('configurator/layer_option')
                            ->setLayerId($this->getId())
                            ->setName($option_name);
                
                if(!$option['delete'][$option_id]){                                                            
                    if(is_numeric($option_id)){
                        $model->setId($option_id);
                    }
                    $model->save();
                }elseif(is_numeric($option_id)){
                    $model->load($option_id)->delete();
                }
            }
        }
        
        $original_option_ids = $this->getOptionCollection()->getColumnValues('option_id');
        
        //extend options
        $fields = array_keys(Mage::getResourceModel('configurator/layer_option')->getFields());            
        foreach($fields as $field){
            if(isset($data[$field]) && is_array($data[$field])){
                foreach($data[$field] as $option_id => $value){
                    if(in_array($option_id,$original_option_ids)){
                        $model = Mage::getModel('configurator/layer_option')
                            ->setLayerId($this->getId())
                            ->setId($option_id)
                            ->setData($field,$value)
                            ->save();
                    }

                }
            }
        }                    
        return parent::_afterSave();
    }
}