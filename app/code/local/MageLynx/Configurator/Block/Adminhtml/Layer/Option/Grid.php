<?php
class MageLynx_Configurator_Block_Adminhtml_Layer_Option_Grid extends Mage_Adminhtml_Block_Widget_Grid
{            
    public function __construct(){
        parent::__construct();
        $this->setId('optionGrid');
        $this->setDefaultSort('option_id');
        $this->setDefaultDir('ASC');             
        $this->setUseAjax(true);
    }               
            
    protected function _prepareCollection(){
        $optionCollection = Mage::registry('layer')->getOptionCollection();             
        $this->setCollection($optionCollection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
                
        $this->addColumn('option_id', array(
            'header'=>Mage::helper('configurator')->__('#'),
            'sortable'=>true,
            'index'=>'option_id',
            'type'      => 'number',
            'width'     => '20px',
        ));        
        
        
        $this->addColumn("title", array(
            'header'=>Mage::helper('configurator')->__("Name"),
            'index'=>"name"
        ));
        
        
        $this->addColumn("base_image", array(
            'sortable'=>false,                    
            'filter' => false,
            "renderer" => "configurator/adminhtml_widget_grid_column_renderer_image",
            'header'=>Mage::helper('configurator')->__("Base Image (SVG)"),
            'index'=>"base_image"
        ));
        
        $this->addColumn("overlay", array(
            'sortable'=>false,                    
            'filter' => false,
            "renderer" => "configurator/adminhtml_widget_grid_column_renderer_image",
            'header'=>Mage::helper('configurator')->__("Overlay (PNG)"),
            'index'=>"overlay"
        ));
                
        
        $this->addColumn("combination_determine", array(
            'sortable'=>false,                    
            'filter' => false,
            "renderer" => "configurator/adminhtml_widget_grid_column_renderer_combinations",
            'header'=>Mage::helper('configurator')->__("Determined by"),
            'index'=>"combination_determine",            
        ));        

        return $this;
        
    }
                                        
    public function getGridUrl(){
        return $this->getUrl('configurator/adminhtml_layer/optionGrid', array('_current'=>true, 'layer_id' => Mage::registry('layer')->getId()));
    }
    
    public function getRowUrl($row){
        return false;
    }        

}
