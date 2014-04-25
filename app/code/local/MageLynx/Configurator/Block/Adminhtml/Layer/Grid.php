<?php
class MageLynx_Configurator_Block_Adminhtml_Layer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{            
    public function __construct(){
        parent::__construct();
        $this->setId('layerGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');             
        $this->setUseAjax(true);
    }               
        
    
    protected function _prepareCollection(){
        $optionCollection = Mage::getResourceModel('configurator/layer_collection');
     
        $this->setCollection($optionCollection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
                
        $this->addColumn('layer_id', array(
            'header'=>Mage::helper('configurator')->__('#'),
            'sortable'=>true,
            'index'=>'layer_id',
            'type'      => 'number',
            'width'     => '20px',
        ));        
                
        $this->addColumn("name", array(
            'header'=>Mage::helper('configurator')->__("Name"),
            'index'=>"name"
        ));
        
        $this->addColumn("code", array(
            'header'=>Mage::helper('configurator')->__("Code"),
            'index'=>"code"
        ));
                
        return $this;
        
    }
                                        
    public function getGridUrl(){
        return $this->getUrl('configurator/adminhtml_layer/grid', array('_current'=>true));
    }
    
    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('layer_id'=>$row->getId()));
    }        

}
