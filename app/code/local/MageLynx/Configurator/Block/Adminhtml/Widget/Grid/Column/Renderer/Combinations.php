<?php
class MageLynx_Configurator_Block_Adminhtml_Widget_Grid_Column_Renderer_Combinations extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{    
    public function renderCss(){
        return $this->getColumn()->getIndex() . " combination-column";
    }
    public function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);
        
        $return ="";
        
        $input_id = $this->getColumn()->getIndex()."_".$row->getId();
        $input_name = $this->getColumn()->getIndex()."[{$row->getId()}]";
        $value = parent::_getValue($row);
        
        $return .="<input type='hidden' class='name-hint' value='{$row->getData('value')}'/>"; 
        $return .="<input type='hidden' id='{$input_id}' name='{$input_name}' value='{$value}'/>";
        $return .="<div class='grid-combination-preview'></div></div><div class='decorator'></div>";
        return $return;
    }
}
