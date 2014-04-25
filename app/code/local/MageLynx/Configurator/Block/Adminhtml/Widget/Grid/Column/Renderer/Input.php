<?php
class MageLynx_Configurator_Block_Adminhtml_Widget_Grid_Column_Renderer_Input   extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text{
    public function render(Varien_Object $row){
        $value = parent::_getValue($row);
        
        $input_id = $this->getColumn()->getIndex()."_".$row->getId();
        $input_name = $this->getColumn()->getIndex()."[{$row->getId()}]";
        
        $return = '';
        
        $return .="<input type='text' id='{$input_id}' class='input-text' name='{$input_name}' value='{$value}'/>";
        return $return;
        
    }

}
