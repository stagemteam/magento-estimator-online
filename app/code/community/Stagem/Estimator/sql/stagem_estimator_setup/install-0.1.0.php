<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('stagem_estimator/addon');
$estimationTableName = $installer->getTable('stagem_estimator/estimation');
$date = new DateTime();
$defaultStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;

$installer->startSetup();

$addonTable = $installer->getConnection()
    ->newTable($tableName)
    ->setComment('Online Estimator Add-ons')
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ], 'Autoincrement')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Store id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
        'nullable' => false,
    ], 'Date of addon creation')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
        'nullable' => false,
    ], 'Date of addon updation')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_CHAR, 16, [
        'nullable' => false,
    ], 'Addon type (input, checkbox, radiobutton, select, input-select)')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Addon name')
    ->addColumn('price_condition', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Price condition')
    ->addColumn('free', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Free amount of units')
    ->addColumn('round_up', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, [
        'nullable' => false,
        'default' => 0,
    ], 'Round up to a larger number')
    ->addColumn('is_separate', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, [
        'nullable' => false,
        'default' => 0,
    ], 'Show addon in a separate block')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, [
        'nullable' => false,
        'default' => 0,
    ], 'Is addon active?')
    ->addColumn('placeholder', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
        'default' => '',
    ], 'Placeholder for form element')
    ->addColumn('info_message', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => true,
    ], 'Comment after a form element')
    ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, [
        'nullable' => false,
        'default' => 0,
    ], 'Ppriority');
$installer->getConnection()->createTable($addonTable);

$estimationTable = $installer->getConnection()
    ->newTable($estimationTableName)
    ->setComment('Estimations of Customers')
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ], 'Autoincrement')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Store ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
        'nullable' => false,
    ], 'Date of addon creation')
    ->addColumn('configurable_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, [
        'nullable' => false,
    ], 'Configurable Product ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, [
        'nullable' => false,
    ], 'Simple Product ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, [
        'nullable' => false,
    ], 'Category ID')
    ->addColumn('manufacturer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, [
        'nullable' => false,
    ], 'Manufacturer ID')
    ->addColumn('is_ready_to_install', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, [
        'nullable' => false,
        'default' => 0,
    ], 'Ready to install?')
    ->addColumn('hash', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Unique estimation hash')
    ->addColumn('addons', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => false,
    ], 'Selected addons in JSON format')
    ->addColumn('customer', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => false,
    ], 'Customer info in JSON format')
    ->addColumn('files', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => true,
    ], 'Path to a photo on the server')
;
$installer->getConnection()->createTable($estimationTable);


$sql = <<<SQL
INSERT INTO `{$tableName}` (`store_id`, `created_at`, `updated_at`, `type`, `name`, `price_condition`, `round_up`, `free`, `is_active`, `placeholder`, `info_message`) VALUES
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'radio',
    'What type of house you have',
    '0 | Bungalow
0 | Two storey apartment
0 | Two storey house
0 | Condo',
    '0', -- round_up
    '0', -- free
    '1', -- is_active  
    '', -- placeholder
    '' -- info_message
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'date',
    'Which time is suite for you for installation',
    '0', -- price_condition
    '0', -- round_up
    '0', -- free
    '1', -- is_active  
    '', -- placeholder
    '' -- info_message
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'radio',
    'Type of wall where the air-conditioner will be placed',
    '0 | Outdoors
0 | Indoors',
    '0',
    '0',
    '1',
    '',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'text',
    'Additional requirement of pipes and wires',
    '10 | 1 pc = 3 ft',
    '1',
    '5',
    '1',
    'type the length in feet, e.g 15',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'text',
    'Length from the electronic shield to the outdoor unit',
    '5 | ft',
    '1',
    '0',
    '1',
    'type the length in feet, e.g 15',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'text',
    'Additional required aluminum cover (white/brown)',
    '40 | 1 pc = 3 ft',
    '1',
    '5',
    '0',
    'type the length in feet, e.g 15',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Aluminum table add-on',
    '60',
    '0',
    '0',
    '1',
    '',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Base for aluminum table add-on',
    '60',
    '0',
    '0',
    '0',
    '',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Drain Pump',
    '150',
    '0',
    '0',
    '0',
    '',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Work in the attic',
    '150',
    '0',
    '0',
    '0',
    '',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Work on the roof',
    '150',
    '0',
    '0',
    '0',
    '',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Installation from heights',
    '150',
    '0',
    '0',
    '0',
    '',
    ''
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'textarea',
    'Extra equipment to work',
    '',
    '0',
    '0',
    '0',
    'Extra equipment to lift unit to elevated surface/roof.
Extra equipment to work from heights etc.',
    'If the units (inside and outside) will be on the same wall. How far is the electrical panel from the outside unit? How long will be the pipes between the inside and outside unit'
    );
SQL;
$installer->run($sql);
$installer->endSetup();
