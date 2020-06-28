<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2020 Serhii Popov
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

class Stagem_Estimator_Block_Estimator extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $this->setTemplate('followupemail/related.phtml');
        return $this->renderView();
    }

    public function getItems()
    {
        return $this->_productCollection;
    }
}
