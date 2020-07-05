<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2020 Stagem
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

/**
 * @method Stagem_Estimator_Model_Estimation getEstimation()
 */
class Stagem_Estimator_Block_Email_Addons extends Mage_Core_Block_Template
{
    /**
     * @param Stagem_Estimator_Model_Addon $addon
     * @param int|string $input
     */
    public function getPriceValue($addon)
    {
        $estimation = $this->getEstimation();
        //$priceCondition = $addon->get
        $selectedValue = $estimation->getSelectedAddons()[$addon->getId()];

        $price = $addon->calculate($selectedValue);

        if ($price == Stagem_Estimator_Model_Addon::PRICE_FREE) {
            $price = $this->__('Free');
        } elseif ($price === Stagem_Estimator_Model_Addon::PRICE_VARIABLE) {
            $price = $this->__('Variable');
        } else {
            $price = Mage::helper('core')->currency($price, true, false);
        }

        return $price;
    }

    /**
     * @param Stagem_Estimator_Model_Addon $addon
     * @param int|string $input
     */
    public function getMeasureValue($addon)
    {
        if (!$addon->isMultiple() && in_array($addon->getType(), [
            Stagem_Estimator_Model_Addon::TYPE_RADIO,
            Stagem_Estimator_Model_Addon::TYPE_CHECKBOX,
        ])) {
            return null;
        }

        $estimation = $this->getEstimation();
        $selectedValue = $estimation->getSelectedAddons()[$addon->getId()];

        $conditions = $addon->getPriceConditions();

        $condition = $addon->isMultiple()
            ? $conditions[$selectedValue]
            : array_shift($conditions);

        $parsedCondition = $addon->parsePriceCondition($condition);

        $unit = $parsedCondition['from_unit'] ?? $selectedValue;
        if (isset($parsedCondition['to_unit'])) {
            $unit = $selectedValue . ' ' . $this->__($parsedCondition['to_unit']);
        }

        return ' - ' . $unit;
    }
}
