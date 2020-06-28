<?php

/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */
class Stagem_Estimator_Model_Addon extends Mage_CatalogRule_Model_Rule
{
    const TYPE_INPUT = 'input';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    const TYPE_SELECT = 'select';
    const TYPE_INPUT_SELECT = 'input-select'; // @TODO

    protected function _construct()
    {
        $this->_init('stagem_estimator/addon');
    }

    /*public function getConditionsInstance()
    {
        return Mage::getModel('catalogrule/rule_condition_combine');
    }*/
}
