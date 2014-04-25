<?php 
//die('test');
require_once './abstract.php';

class Configurator extends Configurator_Abstract{    
    
    protected function _postPrepareExec(&$exec){
        
    }
    
    public function getOptionByAttributeCode($attribute_code){
        $attributes_data = $this->getAttributesData();
        $parameters = $this->getConfiguredParameters();
        $mapping  = $this->getAttributeCodeIdMapping();
        
        $__attribute_id = $mapping[$attribute_code];
        $__option_id = isset($parameters[$__attribute_id]) ? $parameters[$__attribute_id] : false;
        //var_dump($__option_id);
        if($__option_id){
            $__option = $attributes_data[$__attribute_id]['options'][$__option_id];            
            return $__option;
        }
        
        return false;
                
    }
        
    public function getAttributeCodeIdMapping(){
        if(!isset($this->_attribute_code_mapping)){
            $attributes_data = $this->getAttributesData();
        
            $attribute_code_id = array();
            foreach($attributes_data as $attribute_id => $attribute_dad){
                $attribute_code_id[$attribute_dad['code']] = $attribute_id;
            }
            $this->_attribute_code_mapping = $attribute_code_id;
        }        
        return $this->_attribute_code_mapping;
    }
    
    
    protected function _beforeOverlaySvgProceed(&$svg, $layer_code, $layer_data){
       // var_dump($layer_data); die('xxxx');
        if($layer_code == 'cloth_base'){
            $print_selected_option = $this->getOptionByAttributeCode('cloth_print');
            if($print_selected_option['swatch']){
		
		$print_url = $this->resize($this->getBaseMediaPath().$print_selected_option['swatch'], 125,125);
                $replacements_data = array(
                                        '{x}'           => 152,
                                        '{y}'           => 100,
                                        '{width}'       => '125',
                                        '{height}'      => '125',
                                        '{image}'       => $print_url//$this->getBaseMediaPath().$print_selected_option['swatch']
                                    );

                $replacements_data['{id}'] = 'img'.md5(join($replacements_data));

                $img_tag = '<image x="{x}" y="{y}" id="{id}" height="{height}" width="{width}" xlink:href="{image}" />';
//                $img_tag = '<image x="{x}" y="{y}" id="{id}"  width="{width}" xlink:href="{image}" />';

                $img_tag = str_replace(array_keys($replacements_data), array_values($replacements_data), $img_tag);

                $this->addTag($svg,$img_tag);
            }            
            //var_dump($print_selected_option);
            //var_dump($this->getBaseMediaPath().$print_selected_option['swatch']);
            //var_dump();
        }
    }
}

$configurator = new Configurator();

echo Zend_Json::encode($configurator->getGeneratedImageData());
?>
