<?php

    require_once '../app/Mage.php';

    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    Mage::getSingleton('core/session', array('name'=>'adminhtml'));
    $userModel = Mage::getModel('admin/user');
    $userModel->setUserId(1);
    $session = Mage::getSingleton('admin/session');
    $session->setUser($userModel);
    $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());

     //$userModel->setData("username", "admin");
     //$userModel->setData("password", "q1w2e3r4");
     //$userModel->save();

//    echo "<pre>";    
    $connection = Mage::getSingleton('core/resource')->getConnection("core_read");
    //$connection->query("UPDATE `core_resource` set version = '0.1.4', data_version = '0.1.4' where code = 'misc_setup' ");
    //var_dump($connection);
    $res=$connection->query("show tables")->fetchAll();
    echo "<pre>";
    var_dump($res);
    echo "</pre>";
//    var_dump($connection->query("SELECT data_index FROM `catalogsearch_fulltext` as `s` where (s.data_index REGEXP '[^0-9]+10[^0-9]+'  AND `s`.`data_index` LIKE '%ohm%')")->fetchAll());
//var_dump($connection->dropForeignKey($connection->getTableName('core/cache_tag'), $connection->getForeignKeyName('core/cache_tag', 'cache_id', 'core/cache', 'id')));
    //var_dump($connection->query("delete from catalogsearch_result;"));
//    echo "</pre>";
?>
