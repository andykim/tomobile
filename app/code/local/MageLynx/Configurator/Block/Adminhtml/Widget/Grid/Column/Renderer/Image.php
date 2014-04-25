<?php
class MageLynx_Configurator_Block_Adminhtml_Widget_Grid_Column_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{    
    
    //this renderer is meant to be used together with swfupload
    public function render(Varien_Object $row){
        $short_path = parent::_getValue($row);                                
        
        $img_id = 'img_'.$this->getColumn()->getIndex()."_".$row->getId();
        $input_id = $this->getColumn()->getIndex()."_".$row->getId();
        $input_name = $this->getColumn()->getIndex()."[{$row->getId()}]";
        
        $onclick = "onclick=\"uploader_input=$('{$input_id}'); uploader_img_element=$('{$img_id}'); showUploader();\"";
        $return = "<a class=\"upload-link\" href=\"javascript:void(0);\" {$onclick} >{$this->__('Upload')}</a>";
        
        $onclick = "onclick=\"$('{$input_id}').value=''; $(this).next('img').hide();\"";
        $return .= " <a style=\"margin-left: 3px; border:none\" href=\"javascript:void(0);\" {$onclick} >[ x ]</a>";
                
        if(empty($short_path)){
            $return .= "<img src=\"\" style=\"display:none;\" id=\"{$img_id}\" />";
        }else{
            $src = Mage::helper('configurator/image')->resize($short_path,MageLynx_Configurator_Helper_Image::IMAGE_TYPE_OPTION_SWATCH_BACKEND_SIZE);
            $return .= "<img src=\"{$src}\" id=\"{$img_id}\" />";
        }
                
        
        $return .= "<input type=\"hidden\" id=\"{$input_id}\" name=\"{$input_name}\" value=\"{$short_path}\" />";
                
        
        return $return;
        
    }

}
