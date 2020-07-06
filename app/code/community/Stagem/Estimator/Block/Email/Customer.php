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
class Stagem_Estimator_Block_Email_Customer extends Mage_Core_Block_Template
{
    /**
     * @param int|string $input
     */
    public function getLabel($index)
    {
        $label = ucfirst($index);
        $label = str_replace('_', ' ', $label);
        return $this->__($label);
    }

    /**
     * @param int|string $input
     */
    public function getValue($index)
    {
        $customer = $this->getItems();
        
        return $customer[$index]; 
    }
}
