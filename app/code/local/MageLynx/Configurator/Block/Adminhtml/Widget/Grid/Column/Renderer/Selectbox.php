<?php
class MageLynx_Configurator_Block_Adminhtml_Widget_Grid_Column_Renderer_Selectbox   extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text{
    public function render(Varien_Object $row){
        $value = parent::_getValue($row);
        $options = $this->getColumn()->getOptions();
        
        $return = '';
        foreach($options as $id => $_value){
            $selected = $id == $value ? " selected='selected' " : "";
            $return .= "<option value='{$id}' {$selected}>{$_value}</option>";
        }        
        
        $input_id = $this->getColumn()->getIndex()."_".$row->getId();
        $input_name = $this->getColumn()->getIndex()."[{$row->getId()}]";
        
        $return = "<select onchange=\"$('{$input_id}').value=this.value\">".$return."</select>";
        $return .="<input type='hidden' id='{$input_id}' name='{$input_name}' value='{$value}'/>";
        return $return;
        
    }

}
