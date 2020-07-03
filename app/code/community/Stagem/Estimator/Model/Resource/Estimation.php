<?php
/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */

class Stagem_Estimator_Model_Resource_Estimation extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('stagem_estimator/estimation', 'id');
    }
}
