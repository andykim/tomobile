<?php
class MageLynx_Configurator_Helper_Image extends Mage_Core_Helper_Abstract{ 
    
    const IMAGE_TYPE_OPTION_SWATCH = 'swatch';
    const IMAGE_TYPE_OPTION_SWATCH_BACKEND_SIZE = '50x50';
    
    public function getConvertPath(){
        return 'convert';
    }
    
    public function preparePath($dir_path){
        
        $ioAdapter = new Varien_Io_File();                    
        $ioAdapter->setAllowCreateFolders(true);
        $ioAdapter->open(array(
            'path'=> $dir_path
        ));
    }
    
    public function getFileExtension($filename){        
        return strtolower(substr($filename, strrpos($filename, '.')+1));
    }
    
    public function save($type, $_files_global_key){
        $uploader = new Mage_Core_Model_File_Uploader($_files_global_key);
        $uploader->setAllowedExtensions($this->getImageExtensions());
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        
        $dir_path = Mage::getBaseDir('media').DS.'configurator'.DS.$type.DS;
        $this->preparePath($dir_path);        
        $result = $uploader->save($dir_path);
        
        $result['short_path'] = str_replace(DS.DS, DS,$result['path'].$result['file']);
        $result['short_path'] = str_replace(Mage::getBaseDir('media'),'',$result['short_path'] );
        
        return $result;
    }
    
    public function resize($short_path, $sizex,$sizey=null){        
        
        $full_img_path = '';
        if($this->getFileExtension($short_path) == 'svg'){
            $old_img_path = Mage::getBaseDir('media').$short_path;
            $new_img_path = preg_replace('/\.svg/i','_scalar.png', $old_img_path);
            $short_path = preg_replace('/\.svg/i','_scalar.png', $short_path);
            if(!is_file($new_img_path)){
                //generate scalar png from vector svg
                exec("{$this->getConvertPath()} $old_img_path $new_img_path");                
                $full_img_path = $new_img_path;
            }
        }
        
        if(empty($full_img_path)){
            $full_img_path = Mage::getBaseDir('media').$short_path;
        }
                
        $size = $sizex . ($sizey ? "x".$sizey:'');
        $short_path = str_replace(DS.'configurator','',$short_path);
        $resized_img_path = Mage::getBaseDir('media').DS.'configurator'.DS.'resized'.DS.dirname($short_path).DS.$size."_".basename($short_path);
        if(!is_file($resized_img_path)){
            $this->preparePath(dirname($resized_img_path));

            $command = "{$this->getConvertPath()} -resize $size -gravity center -background none -extent $size $full_img_path $resized_img_path";            
            exec($command);            
        }
        
        $img_src = str_replace(Mage::getBaseDir('media').DS, Mage::getBaseUrl('media'), $resized_img_path);
        $img_src = str_replace(DS, "/",$img_src);
        
        return $img_src;
    }  
    
    public function getUrl($short_path){
        return Mage::getBaseUrl('media').str_replace(DS, "/",$short_path);
    }
    public function getImageExtensions(){
        return array('jpg','jpeg','gif','png', 'svg');
    }
}
