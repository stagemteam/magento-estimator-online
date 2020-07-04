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

class Stagem_Estimator_Block_Email_Addon extends Mage_Core_Block_Template
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

        if ($price === Stagem_Estimator_Model_Addon::PRICE_FREE) {
            $price = $this->__('free');
        } elseif ($price === Stagem_Estimator_Model_Addon::PRICE_VARIABLE) {
            $price = $this->__('variable');
        } else {
            $price = $price . '$';
        }

        return $price;
    }

    /**
     * @param Stagem_Estimator_Model_Addon $addon
     * @param int|string $input
     */
    public function getMeasureValue($addon)
    {
        $estimation = $this->getEstimation();
        $selectedValue = $estimation->getSelectedAddons()[$addon->getId()];

        $priceConditions = $addon->getPriceConditions();
        //$label = $addon->getName();

        $priceCondition = [];
        $priceCondition = $addon->isMultiple()
            ? $priceConditions[$selectedValue]
            : $priceCondition = array_shift($priceConditions);

        $parsedCondition = $addon->parsePriceCondition($priceCondition);

        $unit = $this->__($priceCondition['from_unit']);
        if (isset($priceCondition['to'])) {
            $unit = $selectedValue . ' ' . $unit;
        }

        return $unit;
    }
}
