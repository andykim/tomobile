<?php
class MageLynx_Configurator_Block_Adminhtml_Attribute_Option_Grid extends Mage_Adminhtml_Block_Widget_Grid
{            
    public function __construct(){
        parent::__construct();
        $this->setId('optionGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');             
        $this->setUseAjax(true);
    }               
    
    public function getAttributeId(){        
        if(!$attribute_id = Mage::app()->getFrontController()->getRequest()->getParam('attribute_id')){
            $attribute_id = Mage::registry("attribute_id");
        }
        return $attribute_id;
    }
    
    protected function _prepareCollection(){
        $optionCollection = Mage::getResourceModel('configurator/attribute_option_collection')
                ->addFieldToFilter('main_table.attribute_id', array("eq"=>$this->getAttributeId()));
     
        $this->setCollection($optionCollection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
                
        $attribute_add_data = Mage::getModel('configurator/attribute')->load($this->getAttributeId());
        
        $this->addColumn('sort_order', array(
            'header'=>Mage::helper('configurator')->__('#'),
            'sortable'=>true,
            'index'=>'sort_order',
            'type'      => 'number',
            'width'     => '20px',
        ));        
        
        
        //add label
        $this->addColumn("value", array(
            'header'=>Mage::helper('configurator')->__("Label"),
            'index'=>"value"
        ));
        
        
        $this->addColumn("swatch", array(
            'sortable'=>false,                    
            'filter' => false,
            "renderer" => "configurator/adminhtml_widget_grid_column_renderer_image",
            'header'=>Mage::helper('configurator')->__("Swatch"),
            'index'=>"swatch"
        ));
        
        $this->addColumn("default_state", array(
            'sortable'=>false,                    
            'type' => 'options',
            "renderer" => "configurator/adminhtml_widget_grid_column_renderer_selectbox",
            'header'=>Mage::helper('configurator')->__("Default state"),
            'index'=>"default_state",
            "options" => array(
                        1 => Mage::helper("configurator")->__("Enabled"),
                        0 => Mage::helper("configurator")->__("Disabled"),
                    )
        ));
        
        $this->addColumn("combination_enable", array(
            'sortable'=>false,                    
            'filter' => false,
            "renderer" => "configurator/adminhtml_widget_grid_column_renderer_combinations",
            'header'=>Mage::helper('configurator')->__("Enabled by"),
            'index'=>"combination_enable",            
        ));
        
        $this->addColumn("combination_disable", array(
            'sortable'=>false,                    
            'filter' => false,
            "renderer" => "configurator/adminhtml_widget_grid_column_renderer_combinations",
            'header'=>Mage::helper('configurator')->__("Disabled by"),
            'index'=>"combination_disable",            
        ));
        
        if($attribute_add_data->getData('inventory_enabled')){
            $this->addColumn("qty", array(                
                "renderer" => "configurator/adminhtml_widget_grid_column_renderer_input",
                'header'=>Mage::helper('configurator')->__("Qty"),
                'index'=>"qty",            
                'type'      => 'number',
            ));
        }
        if($attribute_add_data->getData('pricing_enabled')){
            $this->addColumn("price", array(                
                "renderer" => "configurator/adminhtml_widget_grid_column_renderer_input",
                'header'=>Mage::helper('configurator')->__("Price"),
                'index'=>"price",
                'type'      => 'number',
            ));
        }
        
        return $this;
        
    }
                                        
    public function getGridUrl(){
        return $this->getUrl('configurator/adminhtml_attribute/optionGrid', array('_current'=>true, 'attribute_id' => $this->getAttributeId()));
    }
    
    public function getRowUrl($row){
        return false;
    }        

}
