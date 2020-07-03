<?php

/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */
class Stagem_Estimator_Model_Addon extends Mage_CatalogRule_Model_Rule
{
    // @TODO Move it to config.xml and make dynamic
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    const TYPE_SELECT = 'select';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_INPUT_SELECT = 'input-select'; // @TODO

    protected function _construct()
    {
        $this->_init('stagem_estimator/addon');
    }

    public function isSeparate()
    {
        return (bool) $this->getData('is_separate');
    }

    public function isMultiple()
    {
        $variations = explode("\n", $this->getPriceCondition());

        // If we have more than one element in array, then it is multiple. There is no need count all elements.
        return isset($variations[1]);
    }

    /**
     * Prepare value for form element.
     *
     * It returns addon ID if there is no variations, otherwise array return.
     *
     * @return int|array
     */
    public function getValue()
    {
        $value = $this->getId();

        if ($this->isMultiple()) {
            $value = [];
            $conditions = $this->parsePriceConditions();
            foreach ($conditions as $index => $condition) {
                $value[] = [
                    'value' => $index,
                    'label' => $condition['from_to']
                        ? $condition['from_unit'] . ' - ' . $condition['from_to']
                        : $condition['from_unit'],
                ];
            }
        }

        return $value;
    }

    /**
     * @todo Implement Select and Input Select
     */
    public function calculate($input = null)
    {
        $conditions = $this->parsePriceConditions();

        $price = 0;
        if (in_array($this->getType(), [
            Stagem_Estimator_Model_Addon::TYPE_TEXT,
            Stagem_Estimator_Model_Addon::TYPE_NUMBER,
            Stagem_Estimator_Model_Addon::TYPE_INPUT_SELECT,
        ])) {
            // Input element can have only one texted condition
            $condition = array_shift($conditions);
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
            $condition = $conditions[$input];

            $price = $condition['price'];
        }

        return $price;
    }

    public function parsePriceConditions()
    {
        $conditions = explode("\n", $this->getPriceCondition());

        $parsed = [];
        foreach ($conditions as $condition) {
            $parsed[] = $this->parsePriceCondition($condition);
        }

        return $parsed;
    }

    public function parsePriceCondition($condition)
    {
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

            $value['from'] = trim($left[0]);
            $value['from_unit'] = trim($left[1]);

            $value['to'] = trim($right[0]);
            $value['to_unit'] = trim($right[1]);
        } else {
            $value['from_unit'] = trim($parts[0]);
        }

        return $value;
    }
}