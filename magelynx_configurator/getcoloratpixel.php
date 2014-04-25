<?php
    
    $max_distance = 20;
    
    $file = file_get_contents($_GET['filepath']);
    $x = $_GET['x'];
    $y = $_GET['y'];
    
    $im = imagecreatefromstring($file);
    $rgb = @imagecolorat($im, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    
    $_color = '';
    foreach(explode(",",$_GET['colors']) as $color){
        $_r =  hexdec(substr($color, 0,2));
        $_g =  hexdec(substr($color, 2,2));
        $_b =  hexdec(substr($color, 4,2));
        
        $_current_distance = abs($_r- $r)+abs($_g-$g)+abs($_b-$b);
        
        if( (!isset($_distance) || $_distance > $_current_distance)
                && $_current_distance<$max_distance){
            $_distance = $_current_distance; 
            $_color = str_pad(dechex($_r), 2, '0', STR_PAD_LEFT)
                    .str_pad(dechex($_g), 2, '0', STR_PAD_LEFT)
                    .str_pad(dechex($_b), 2, '0', STR_PAD_LEFT);  
        }        
    }

    echo strtolower($_color);