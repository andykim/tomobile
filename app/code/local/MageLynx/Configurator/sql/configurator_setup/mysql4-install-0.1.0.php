<?php
$installer = $this;

$installer->startSetup();

//create options extending table
$installer->getConnection()->dropTable($installer->getTable('configurator/attribute_option'));

$table = $installer->getConnection()
        ->newTable($installer->getTable('configurator/attribute_option'))
        ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Attribute ID')
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false
            ), 'Option ID')
        ->addColumn('swatch', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Swatch')        
        ->addColumn('default_state', Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array(
            'default'   => 1
        ), 'Default state')
        ->addColumn('combination_enable', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Enabling combination')
        ->addColumn('combination_disable', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Disabling combination')
        ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0
        ), 'Inventory QTY')
        ->addColumn('price', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0
        ), 'Price')
        ->addForeignKey($installer->getFkName('configurator/attribute_option', 'attribute_id', 'catalog/eav_attribute', 'attribute_id'),
            'attribute_id', $installer->getTable('catalog/eav_attribute'), 'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('configurator/attribute_option', 'option_id', 'eav_attribute_option', 'option_id'),
            'option_id', $installer->getTable('eav_attribute_option'), 'option_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Configurator Attributes Options Table');

$installer->getConnection()->createTable($table);

//fill the table with values
$collection = Mage::getResourceModel('eav/entity_attribute_collection')
        ->setEntityTypeFilter(Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId())
        ->addFieldToFilter('main_table.frontend_input', "select")
        ->addFieldToFilter('main_table.source_model', "eav/entity_attribute_source_table");


foreach($collection->getItems() as $attribute){
    try{
        $options = $attribute->getSource()->getAllOptions();        
        foreach($options as $option){
            $option_id = $option['value'];       
            if($option_id){
                Mage::getModel('configurator/attribute_option')
                        ->setAttributeId($attribute->getAttributeId())
                        ->setId($option_id)
                        ->save();
            }        
        }    
    }catch(Exception $e){};
}


//layers table
$installer->getConnection()->dropTable($installer->getTable('configurator/layer'));
$table = $installer->getConnection()
        ->newTable($installer->getTable('configurator/layer'))
        ->addColumn('layer_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Layer ID')        
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Name')
        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Code')        
        ->setComment('Configurator Layers Table');

$installer->getConnection()->createTable($table);

//layer options table
$installer->getConnection()->dropTable($installer->getTable('configurator/layer_option'));
$table = $installer->getConnection()
        ->newTable($installer->getTable('configurator/layer_option'))
        ->addColumn('layer_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Layer ID')        
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Option ID')        
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Name')
        ->addColumn('base_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Base Image')
        ->addColumn('overlay', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Overlay')        
        ->addColumn('combination_determine', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Determining combination of options')                
        ->addForeignKey($installer->getFkName('configurator/layer_option', 'layer_id', 'configurator/layer', 'layer_id'),
            'layer_id', $installer->getTable('configurator/layer'), 'layer_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Configurator Layer Options Table');

$installer->getConnection()->createTable($table);
  

//add necessary product attributes

$installer->removeAttribute('catalog_product', 'configurator_attributes');
$installer->addAttribute('catalog_product', 'configurator_attributes', array(
    'type'              => 'varchar',    
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false
));

$installer->removeAttribute('catalog_product', 'configurator_config');
$installer->addAttribute('catalog_product', 'configurator_config', array(
    'type'              => 'text',    
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false
));

$installer->removeAttribute('catalog_product', 'configurator_options');
$installer->addAttribute('catalog_product', 'configurator_options', array(
    'type'              => 'text',    
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
));




//create options extending table
$installer->getConnection()->dropTable($installer->getTable('configurator/attribute'));

$table = $installer->getConnection()
        ->newTable($installer->getTable('configurator/attribute'))
        ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Attribute ID')
        ->addColumn('inventory_enabled', Varien_Db_Ddl_Table::TYPE_BOOLEAN,null, array(
            'nullable'  => false,
            'primary'   => false,
            'default'   => false
            ), 'Is Inventory Enabled')
        ->addColumn('pricing_enabled', Varien_Db_Ddl_Table::TYPE_BOOLEAN,null, array(
            'nullable'  => false,
            'primary'   => false,
            'default'   => false
            ),  'Is Pricing Enabled')
        ->addForeignKey($installer->getFkName('configurator/attribute', 'attribute_id', 'catalog/eav_attribute', 'attribute_id'),
            'attribute_id', $installer->getTable('catalog/eav_attribute'), 'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)        
        ->setComment('Configurator Attributes Table');

$installer->getConnection()->createTable($table);


$installer->endSetup(); 