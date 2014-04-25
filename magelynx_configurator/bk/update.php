<?php 
require_once './abstract.php';

class Configurator extends Configurator_Abstract{    
}

$configurator = new Configurator();

echo Zend_Json::encode($configurator->getGeneratedImageData());
?>