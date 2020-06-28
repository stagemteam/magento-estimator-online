<?php
/**
 * Rule collection
 *
 * @category Popov
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 21.04.14 15:22
 */

class Stagem_Estimator_Model_Resource_Addon_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('stagem_estimator/addon');
    }

    public function addStoreFilter($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = [$store->getId()];
        }
        if (!is_array($store)) {
            $store = [$store];
        }
        $this->addFilter('store_id', ['in' => $store]);

        return $this;
    }
}
