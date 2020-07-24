<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$tableName = $installer->getTable('stagem_estimator/addon');

$installer->startSetup();

$installer->getConnection()->modifyColumn($tableName, 'placeholder', [
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'nullable' => false,
    'default' => '',
]);

$installer->endSetup();
