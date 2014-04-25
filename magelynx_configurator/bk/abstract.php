<?php 
require_once '../app/Mage.php';

abstract class Configurator_Abstract{
    protected $_CONFIG_PATH;
    protected $_HIGHLIGHT_PATH;
    protected $_SVG_PATH;
    protected $_PREVIEW_PATH;
    protected $_COLORMAP_PATH;
    protected $_SH_PATH;
    protected $_GLOBAL_SETTINGS;
    
    protected $_svg_entities = array('path', 'ellipse','polygon');
    
    public function __construct() { 
        
        $this->_CONFIG_PATH = $this->getCachePath()."config".DS;
        
        $this->_ATTRIBUTES_PATH = $this->getCachePath()."attributes".DS;

                
        $this->_HIGHLIGHT_PATH = $this->getCachePath()."highlight".DS;
        $this->_SVG_PATH = $this->getCachePath()."svg".DS;        
        
        $this->_PREVIEW_PATH = $this->getCachePath()."preview".DS;
        $this->prepareDir($this->_PREVIEW_PATH);
        
        $this->_COLORMAP_PATH = $this->getCachePath()."colormap".DS;
        $this->prepareDir($this->_COLORMAP_PATH);
        
        $this->_SH_PATH = $this->getCachePath()."sh".DS;
        
        
        $global_settings_path = $this->getCachePath()."global_settings.cache";
        
        if(is_file($global_settings_path)){
            $global_settings = file_get_contents($global_settings_path);
            $global_settings = unserialize($global_settings);
        }else{
            $this->initMage();
            $helper = Mage::helper('configurator');
            
            $color_mapping = array();
            if($color_attribute = $helper->getColorAttribute()){
                foreach($helper->getOptionCollection($color_attribute)->getItems() as $option_data){
                    $color_mapping[$option_data->getOptionId()] = $option_data->getSwatch();
                }
            }
            
            $global_settings = array(
                'base_url'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
                'zoom_ratio'  => Mage::getStoreConfig('configurator/settings/zoom_ratio'),
                'width'  => Mage::getStoreConfig('configurator/settings/width'),
                'height'  => Mage::getStoreConfig('configurator/settings/height'),
                'color_attribute_mapping' => $color_mapping // option_id => image_path
            );          
            file_put_contents($global_settings_path, serialize($global_settings));
        }
        $this->_GLOBAL_SETTINGS = $global_settings;
    }
    
    public function getAttributesData(){
        if(!isset($this->_attributes_data)){
                    
            if($this->getProductId()){
                $id = $this->getProductId();
                $attributes_data_path = $this->_ATTRIBUTES_PATH."{$id}.cache";

                if(is_file($attributes_data_path)){
                    $attributes_data = file_get_contents($attributes_data_path);
                    $attributes_data = unserialize($attributes_data);
                }
                else{
                    $this->initMage();                    
                    $_product = Mage::getModel('catalog/product')->load($id);                

                    $attributes_ids = explode("," , $_product->getConfiguratorAttributes());        
                    $_attribute_collection = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->addFieldToFilter('main_table.`attribute_id`', array('in'=>$attributes_ids));

                    $attributes_data = array();
                    foreach($_attribute_collection->getItems() as $_attribute){
                        $attributes_data[$_attribute->getAttributeId()]['code'] = $_attribute->getAttributeCode();
                        $attributes_data[$_attribute->getAttributeId()]['options'] = array();
                        $options = Mage::helper('configurator')->getOptionCollection($_attribute)->getItems();
                        foreach($options as $_option){
                            $attributes_data[$_attribute->getAttributeId()]['options'][$_option->getOptionId()] = $_option->getData();
                        }                    
                    }

                    $this->saveToFile($attributes_data_path, serialize($attributes_data));
                }
                
                $this->_attributes_data = $attributes_data;                
            }else{
                $this->_attributes_data = array();
            }
        }
        return $this->_attributes_data;
    }
    
    public function initMage(){
        if(!isset($this->_init_mage)){
            umask(0);
            Mage::app();
        }        
    }
    
    public function prepareDir($dir_path){
        if(!is_dir($dir_path)){
            mkdir($dir_path,0777,true);
        }
    }
    
    public function saveToFile($file_path, $data){
        $this->prepareDir(dirname($file_path));
        file_put_contents($file_path,$data);
    }
    
    public function getProductId(){
        return isset($_REQUEST['id']) && intval($_REQUEST['id']) ? intval($_REQUEST['id']) : false;
    }
        
    public function getPreviewData(){
        if($this->getProductId()){
            $id = $this->getProductId();            
            $preview_data_path = $this->_CONFIG_PATH."{$id}.cache";
            
            if(is_file($preview_data_path)){
                $preview_data = file_get_contents($preview_data_path);
                $preview_data = unserialize($preview_data);
            }
            else{
                $this->initMage();                    
                $_product = Mage::getModel('catalog/product')->load($id);
                $preview_data = Mage::helper('configurator')->getPreviewData($_product);
                $this->saveToFile($preview_data_path, serialize($preview_data));
            }

            return $preview_data;            
        }
        return false;
    }
    
    public function getConfiguredParameters(){
        $restriction_data = array();
        foreach($_REQUEST as $_key => $value){
            if(!in_array($_key, array('id','colors_data'))){//all other keys are attribute ids
                $_key = str_replace('numeric_', '', $_key);
                $restriction_data[$_key] = $value;
            }
        }
        return $restriction_data;
    }
    
    public function getDeterminedOption($layer_options){
        
        $restriction_data = $this->getConfiguredParameters();
        
        $points = 0;
        $return_option = false;
        foreach($layer_options as $option){            
            $_operation_points = $this->getPassingCombinationMaxLength($option,$restriction_data);
            if($_operation_points>=$points){
                $return_option = $option;
                $points=$_operation_points;
            }
        }        
        if($points == 0){
            return false;
        }
        return $return_option;
    }
    
    public function getPassingCombinationMaxLength($option, $restriction_data = array()){
        $_combinations = Zend_Json::decode($option['combination_determine']);
        if(!is_array($_combinations )) return 0;        
                
        $length = 0;
        foreach($_combinations as $_combination){            
            $combination_pass_flag = true;
            foreach($_combination as $attribute_id => $attribute_relation_data){
                $operation = $attribute_relation_data['operation'];
                $operand = $attribute_relation_data['options'];
                if(!isset($restriction_data[$attribute_id]) 
                        || !$this->attributeRelationPass($restriction_data[$attribute_id], $operation,$operand)){
                    $combination_pass_flag = false;
                    break;
                }
            }
            if($combination_pass_flag){
                if($length <= count($_combination)){
                    $length = count($_combination);
                }
            }
        }
        return $length;
    }
    
    public function attributeRelationPass($value, $operation, $operand){
        switch($operation){
            case "in":
                return in_array($value, $operand);
            break;
            case "eq":
                return $value == $operand[0];
            break;
        }
    }
    
    public function getLayersData(){
        $preview_data = $this->getPreviewData();
        
        $result = array();
        foreach($preview_data['layers'] as $layer_code => $layer_data){
            $determined_option = $this->getDeterminedOption($layer_data['options']['items']);
            
            $result[$layer_code] = array(
                'base_image'    => $determined_option['base_image'],
                'overlay'       => $determined_option['overlay'],
                'x'             => $layer_data['x'],
                'y'             => $layer_data['y'],
                'width'         => $layer_data['width'],
                'height'         => $layer_data['height']
            );
        }
        
        return $result;
    }
    
    public function getCachePath(){
        return dirname(__FILE__).DS."cache".DS;
    }
    public function getBasePath(){
        return dirname(dirname(__FILE__)).DS;
    }
    public function getBaseMediaPath(){
        return $this->getBasePath()."media".DS;
    }
    
    public function getFileExtension($filename){        
        return strtolower(substr($filename, strrpos($filename, '.')+1));
    }
    
    public function getAspectPreservedSizes($_original_width,$_original_height,$_to_width,$_to_height){
        $aspect_ratio = $_original_width/$_original_height;
               
        if($_to_width * $aspect_ratio > $_to_height){
            $_to_height = $_to_width/$aspect_ratio;
        }else{
            $_to_width = $_to_height * $aspect_ratio;           
        }
        return array(
            'width' => $_to_height,
            'height'=> $_to_height
        );
    }    
    
    public function setSize(&$svg, $width, $height){
        preg_match("/<svg[^>]+>/i", $svg, $original);                                
        
        $original = $original[0];
        
        preg_match_all("/width=\"([^\"]+)\"/is", $original, $matches);
        $_original_width = $matches[1][0];
        preg_match_all("/height=\"([^\"]+)\"/is", $original, $matches);
        $_original_height = $matches[1][0];
        
        $sizes = $this->getAspectPreservedSizes($_original_width, $_original_height, $width, $height);
        
        $width = $sizes['width'];
        $heigh = $sizes['height'];
        
        $replacement = $original;
        
        //fullfill viewBox param if needed
        if(!preg_match("/viewBox/is", $original)){                        
            $replacement = str_replace(">", " viewBox=\"0 0 $_original_width $_original_height\"".">",$replacement);
            
        }
        
        
        $replacement = preg_replace("/width=\"[^\"]+\"/is", "width=\"{$width}\"", $replacement);
        $replacement = preg_replace("/height=\"[^\"]+\"/is", "height=\"{$height}\"", $replacement);        
        $svg = str_replace($original, $replacement, $svg);        
    }
    
    public function leaveOneColor(&$svg, $color_index){
        $entities = "(".join("|", $this->_svg_entities).")";
        preg_match_all("/<{$entities}[^>]*#{$color_index}[^>]*>/is", $svg, $matches);                
        $paths = $matches[0];
        
        preg_match_all("/<{$entities}[^>]*>/is", $svg, $matches);
        foreach($matches[0] as $_path){
            if(in_array($_path, $paths)) continue;            
            $svg = str_replace($_path, "", $svg);
        }                
    }
    public function addTag(&$svg, $tag){
        $svg = str_replace("</svg>", $tag."</svg>", $svg);
    }
    
    public function addImage(&$svg,$path, $width = null, $height = null){
        $xlink_declaration = "xmlns:xlink=\"http://www.w3.org/1999/xlink\"";
        
        if(!substr_count($svg,$xlink_declaration)){
            $svg = str_replace("<svg", "<svg " .$xlink_declaration. " ", $svg);
        }
        $id = md5($path);
        
        if($width == null || $height == null){
            $info = getimagesize($path);
        }        
        
        if($width == null){
            $width = $info[0];
        }
        
        if($height == null){            
            $height = $info[1];
        }
                

        preg_match("/<defs[^>]*>.*<\/defs>/is", $svg, $matches);
        if(empty($matches)){
            
            preg_match_all("/<svg[^>]+>/", $svg, $matches);
            $svg = preg_replace("/<svg[^>]+>/", '${0}<defs id="def1"></defs>', $svg);
            preg_match("/<defs[^>]*>.*<\/defs>/is", $svg, $matches);
        }

        $original_defs = $matches[0];
        
        $add_tag = '<pattern id="'.$id.'" patternUnits="userSpaceOnUse" width="'.$width.'" height="'.$height.'">'.
            '<image xlink:href="'.$path.'" x="0" y="0" width="'.$width.'" height="'.$height.'" />'.
            '</pattern>';
        
        $replace_defs = str_replace("</defs>", $add_tag ."</defs>", $original_defs);
        
        $svg = str_replace($original_defs, $replace_defs, $svg);
        
        return $id;
    }
    
    public function getColors($svg){
        $entities = "(".join("|", $this->_svg_entities).")";
        preg_match_all("/<{$entities}[^>]*#([a-fA-F0-9]{6})[^>]*>/i", $svg, $matches);
        
        $return = array();
        foreach($matches[2] as $color_index){
            $return[]=strtolower($color_index);
        }
        return array_unique($return);    
    }
    
    public function colorToImage(&$svg, $color_index, $image_id){        
        $entities = "(".join("|", $this->_svg_entities).")";
        preg_match_all("/<{$entities}[^>]*#{$color_index}[^>]*>/is", $svg, $matches);
        
        $paths = $matches[0];
        
        $replace_paths = array();
        foreach($paths as $index=>$path){
            $replace_paths[$index] = preg_replace("/#{$color_index}/is", 'url(#'.$image_id.')', $path);
        }
        
        $svg = str_replace($paths, $replace_paths,$svg);
    }
    
    public function getHighLights($svg, $width, $height){
        $colors = $this->getColors($svg);
       
        $path = $this->getBaseMediaPath().'configurator'.DS.'default_pattern.gif';        
        
        $return = array();
        
        $dirname = $this->_HIGHLIGHT_PATH.md5($svg).DS;
        
        $flag_create_files = false;
        if(!is_dir($dirname)){
            mkdir($dirname, 0777, true);      
            $flag_create_files = true;
        }
        
        foreach($colors as $color_index){
            $part_file = $dirname.$color_index.".png";                            

            if($flag_create_files && !is_file($part_file)){
                $_svg = $svg;
                $this->leaveOneColor($_svg, $color_index);

                $image_id = $this->addImage($_svg, $path);
                $this->colorToImage($_svg, $color_index, $image_id);            
                $this->setSize($_svg, $width, $height);

                $svgfile = $this->_SVG_PATH.md5($_svg).".svg";

                $this->saveToFile($svgfile, $_svg);


                exec("{$this->getConvertPath()} -background none {$svgfile} -resize {$width}x{$height} -gravity center -extent {$width}x{$height} $part_file ");
                unlink($svgfile);
            }

            $return[$color_index] = $this->pathToUrl($part_file);
        }
        
                
        return $return;
    }
    
    public function getGeneratedImageData(){                                        
        $layers_data = $this->getLayersData();
        $replacements = isset($_REQUEST['colors_data']) ? Zend_Json::decode($_REQUEST['colors_data']) : array();                
        
        $summary_width = 0;
        $summary_height = 0;
        foreach($layers_data as $layer_code => $layer_data){
            if($layer_data['x'] + $layer_data['width'] > $summary_width){
                $summary_width = $layer_data['x'] + $layer_data['width'];
            }
            if($layer_data['y'] + $layer_data['height'] > $summary_height){
                $summary_height = $layer_data['y'] + $layer_data['height'];
            }
        }
        
        $zoom_ratio = max($summary_width/$this->_GLOBAL_SETTINGS['width'], $summary_height/$this->_GLOBAL_SETTINGS['height']);        
        
        $exec = array(
            'images'    => array(),
            'sizes'      => array(),
            'offset'    => array()
        );

        $parts_srcs = array();
        $used_colors = array();
        $united_colormap = array();
        $positioning = array();
        
        foreach($layers_data as $layer_code => $layer_data){
                                 
            $width = $layer_data['width'];
            $height = $layer_data['height'];
            $x = $layer_data['x'];
            $y = $layer_data['y'];
            
            $small_width = intval($width/$zoom_ratio);
            $small_height = intval($height/$zoom_ratio);
            $small_x = intval($layer_data['x']/$zoom_ratio);
            $small_y = intval($layer_data['y']/$zoom_ratio);
            
            $positioning[$layer_code] = array(
                'width'     => $small_width,
                'height'    => $small_height,
                'x'         => $small_x,
                'y'         => $small_y,
            );
            
            try{                
                if('svg' == $this->getFileExtension($layer_data['base_image'])){
                                    
                    $original_svg_path = $this->getBaseMediaPath().$layer_data['base_image'];
                    $svg = file_get_contents($original_svg_path);
                    
                    
                    $colormap_svg = $svg;
                    $this->setSize($colormap_svg, $small_width,$small_height);
                    $this->setSize($svg, $width, $height);
                    
                    //highlights
                    $parts_srcs[$layer_code] = $this->getHighLights($svg, $small_width,$small_height);

                    //colors
                    $svg_colors = $this->getColors($svg);
                    $used_colors = array_merge($used_colors,$svg_colors);

                    $mapping = $this->_GLOBAL_SETTINGS['color_attribute_mapping'];
                    
                    //handle replacements
                    foreach($replacements as $color_index => $option_id){                        
                        if(isset($mapping[$option_id])){
                            $texture_path = $this->getBaseMediaPath().$mapping[$option_id];
                            $color_index  = strtolower($color_index);
                            if(in_array($color_index, $svg_colors)){

                                $id = $this->addImage($svg, $texture_path);

                                $this->colorToImage($svg, $color_index, $id);
                            }
                        }                                                
                    }

                    //handle overlay                                 
                    if(isset($layer_data['overlay']) && $layer_data['overlay']){                        
                        
                        $replacements_data = array(
                                        '{x}'           => 0,
                                        '{y}'           => 0,
                                        '{width}'       => '100%',
                                        '{height}'      => '100%',
                                        '{image}'       => $this->getBaseMediaPath().$layer_data['overlay']
                                    );

                        $replacements_data['{id}'] = 'img'.md5(join($replacements_data));

                        $img_tag = '<image x="{x}" y="{y}" id="{id}" height="{height}" width="{width}" xlink:href="{image}" />';

                        $img_tag = str_replace(array_keys($replacements_data), array_values($replacements_data), $img_tag);

                        $this->addTag($svg,$img_tag);
                    }
                                      
                    $prepared_svg_path = $this->_SVG_PATH.md5($svg).".svg";
                    
                    $exec['images'][] = $prepared_svg_path;
                    $exec['sizes'][] = $width."x".$height;
                    $exec['offset'][] = '+'.$x."+".$y;
                    
                    
                    $colormap_svg_path = $this->_SVG_PATH.md5($colormap_svg).".svg";
                    $united_colormap['images'][] = $colormap_svg_path;
                    $united_colormap['sizes'][] = $small_width."x".$small_height;
                    $united_colormap['offset'][] = '+'.$small_x."+".$small_y;
                    
                                        
                    $this->saveToFile($prepared_svg_path, $svg);
                    $this->saveToFile($colormap_svg_path, $colormap_svg);
                    
                }else{
                    if($layer_data['base_image']){
                        $exec['images'][] = $this->getBaseMediaPath().$layer_data['base_image'];
                    }else{                        
                        $exec['images'][] = $this->getBaseMediaPath().'configurator'.DS.'transparentpixel.gif';
                    }                                        
                    
                    $exec['sizes'][] = $width."x".$height;
                    $exec['offset'][] = '+'.$x."+".$y;                    
                    
                    $united_colormap['images'][] = $this->getBaseMediaPath().'configurator'.DS.'transparentpixel.gif';
                    $united_colormap['sizes'][] = $small_width."x".$small_height;
                    $united_colormap['offset'][] = '+'.$small_x."+".$small_y;
                }                
                
            }catch(Exception $e){
                
            }
        }
        
        $this->_postPrepareExec($exec);
        
        $result_path = $this->_PREVIEW_PATH.md5(serialize($exec)).".png";
        $colormap_path = $this->_COLORMAP_PATH.md5(serialize($united_colormap)).".png";
           
        
        
        if(!is_file($result_path)){
            $this->renderPreview($exec, $result_path);
        }        
        
        if(!is_file($colormap_path)){
            $this->renderPreview($united_colormap, $colormap_path);
        }                        
        
        $used_colors = array_values(array_unique($used_colors));
                
        
        $return['big'] = $this->pathToUrl($result_path);
        if($zoom_ratio !=1){
            $return['small'] = $this->resize($result_path,intval( $summary_width/$zoom_ratio), intval($summary_height/$zoom_ratio));
        }else{
            $return['small'] = $return['big'];
        }     
        $return['used_colors'] = $used_colors;
        $return['parts_srcs'] = $parts_srcs;        
        $return['united_colormap'] = $this->pathToUrl($colormap_path);        
        $return['positioning'] = $positioning;
        
        return $return;
    }
    public function renderPreview($exec, $result_path){                
        
        $command = '#!/bin/bash
            images=("'.join('" "',$exec['images']).'")
            sizes=("'.join('" "',$exec['sizes']).'")
            offset=("'.join('" "',$exec['offset']).'")
                for index in {0..'.(count($exec['images'])-1).'}
                    do
                    if [[ "${images[index]}" == *".svg" ]];
                    then
                        '.$this->getConvertPath().' ${images[index]} -resize ${sizes[index]} -gravity center -background none -extent ${sizes[index]} -repage ${sizes[index]}${offset[index]}\! miff:-
                    else
                        '.$this->getConvertPath().' ${images[index]} -resize ${sizes[index]} -gravity center -background none -extent ${sizes[index]} -repage ${sizes[index]}${offset[index]}\! miff:-
                    fi
                    done |
                    '.$this->getConvertPath().' MIFF:- -layers merge +repage '.$result_path;                        
        
        $sh_path = $this->_SH_PATH.md5(serialize($exec)).".sh";
        
        $command = str_replace("\r", '', $command);
        
        $this->saveToFile($sh_path, $command);
        
        exec('bash ' . $sh_path);                        
    }
    
    public function pathToUrl($path){
        $path = str_replace(DS, '/', $path);
        return str_replace($this->getCachePath(),$this->_GLOBAL_SETTINGS['base_url']."magelynx_configurator/cache/", $path);
    }
    
    public function resize($path, $sizex,$sizey=null){
        $size = $sizex . ($sizey ? "x".$sizey:'');
        $resized_img_path = dirname($path).DS.'resized'.DS.$size."_".basename($path);
        
        
        if(!is_file($resized_img_path)){
            $this->prepareDir(dirname($resized_img_path));

            $command = "{$this->getConvertPath()} -resize $size -gravity center -background none -extent $size $path $resized_img_path";
            exec($command);            
        }        
        return $this->pathToUrl($resized_img_path);
    }
    
    protected function _postPrepareExec(&$exec){
        
    }
    
    public function getConvertPath(){
        return 'convert';
    }
    
}