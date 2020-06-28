<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$table = $installer->getTable('stagem_estimator/addon');
$date = new DateTime();
$defaultStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;

$installer->startSetup();

$rule = $installer->getConnection()
    ->newTable($table)
    ->setComment('Online Estimator Add-ons')
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Autoincrement')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Store id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ), 'Date of addon creation')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ), 'Date of addon updation')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_CHAR, 16, array(
        'nullable'  => false,
    ), 'Addon type (input, checkbox, radiobutton, select, input-select)')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Addon name')
    ->addColumn('price_condition', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Price condition')
    ->addColumn('free', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Free amount of units')
    ->addColumn('round_up', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
        'nullable'  => false,
        'default'  => 0,
    ), 'Round up to a larger number')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
        'nullable'  => false,
        'default'  => 0,
    ), 'Is addon active?')
    ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable'  => false,
        'default'  => 0,
    ), 'Ppriority')
;
$installer->getConnection()->createTable($rule);

$sql = <<<SQL
INSERT INTO `{$table}` (`store_id`, `created_at`, `updated_at`, `type`, `name`, `price_condition`, `round_up`, `free`) VALUES
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'input',
    'Additional requirement of pipes and wires',
    ':10|1=3',
    '1',
    '5'
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'input',
    'Additional required aluminum cover (white/brown)',
    ':40|1=3',
    '1',
    '5'
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Aluminum table add-on',
    ':60',
    '0',
    '0'
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Base for aluminum table add-on',
    ':60',
    '0',
    '0'
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Drain Pump',
    ':150',
    '0',
    '0'
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Work in the attic',
    ':150',
    '0',
    '0'
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Work on the roof',
    ':150',
    '0',
    '0'
    ),
    (
    '{$defaultStoreId}',
    '{$date->format('Y-m-d H:i:s')}',
    '{$date->format('Y-m-d H:i:s')}',
    'checkbox',
    'Installation from heights',
    ':150',
    '0',
    '0'
    );
SQL;
$installer->run($sql);

$installer->endSetup();
