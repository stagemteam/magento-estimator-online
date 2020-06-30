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

    /**
     * @todo Implement Select and Input Select
     */
    public function calculate($input = null)
    {
        $condition = $this->parsePriceCondition();

        $price = 0;
        if (in_array($this->getType(), [
            Stagem_Estimator_Model_Addon::TYPE_INPUT,
            Stagem_Estimator_Model_Addon::TYPE_INPUT_SELECT,
        ])) {
            if (isset($condition['from'])) {
                if ($this->getFree()) {
                    $input = $input - $this->getFree();
                }
                if ($input > 0) {
                    $piece = $input / $condition['to'];
                    if ($this->getRoundUp()) {
                        $piece = ceil($piece);
                    }
                    $price = $piece * $condition['price'];
                }
            } else {
                $price = $input * $condition['price'];
            }
        } else {
            $price = $condition['price'];
        }

        return $price;
    }

    public function parsePriceCondition()
    {
        $condition = $this->getPriceCondition();

        $parsed = [];
        $mainParts = explode('|', $condition);

        if (2 === ($num = count($mainParts))) {
            // Parse rule: 15 | 1 meter = 3 feets
            $parsed['price'] = (float) $mainParts[0];
            $parsed = array_merge($parsed, $this->parseRightPart($mainParts[1]));
        } elseif(strpos($mainParts[0], '=') !== false) {
            // Parse rule: 1 USD = 26 UAH
            $parsed = array_merge($parsed, $this->parseRightPart($mainParts[0]));
            $parsed['price'] = (float) $parsed['to'];
        } else {
            $parsed['price'] = (float) $mainParts[0];
        }

        return $parsed;
    }

    protected function parseRightPart($rightPart)
    {
        $value = [];
        $parts = explode('=', $rightPart);
        if (2 === count($parts)) {
            // parse rule part: 1 meter = 3 feets
            $left = explode(' ', trim($parts[0]));
            $right = explode(' ', trim($parts[1]));

            $value['from'] = $left[0];
            $value['from_unit'] = $left[1];

            $value['to'] = $right[0];
            $value['to_unit'] = $right[1];
        } else {
            $value['name'] = $parts[0];
        }

        return $value;
    }
}
