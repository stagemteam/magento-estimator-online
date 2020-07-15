<?php

/**
 * @author Stagem Team
 * @copyright Copyright (c) 2020 Stagem (https://www.stagem.com.ua)
 * @package Stagem_Estimator
 */
class Stagem_Estimator_Helper_Estimator extends Mage_Core_Helper_Abstract
{
    /**
     * Map DB field -> form field
     * 
     * @var array
     */
    protected $fieldsMap = [
        'category_id' => 'category',
        'manufacturer_id' => 'manufacturer',
        'configurable_id' => 'configurable',
        'product_id' => 'product',
        'addons' => 'addons',
        'customer' => 'customer',
        'files' => 'files',
    ];

    protected $filters = [
        //'phone'=> [
        //    ['self', 'filterNumber']
        //],
        'addons'=> [
            ['self', 'filterAddons']
        ],
    ];

    /**
     * @param $data
     *
     * @return Stagem_Estimator_Model_Estimation
     */
    public function populateFormData($data)
    {
        $estimation = Mage::getModel('stagem_estimator/estimation');
        $estimation->setData('store_id', Mage::app()->getStore()->getId());
        $estimation->setData('created_at', (new DateTime())->format('Y-m-d H:i:s'));
        $estimation->setData('hash', md5(uniqid(rand(), true))); // @link https://stackoverflow.com/a/1846229/1335142

        $formFields = array_flip($this->fieldsMap);
        foreach ($formFields as $formField => $dbField) {
            if (isset($data[$formField])) {

                $value = $data[$formField];
                if (isset($this->filters[$formField])) {
                    $value = $this->doFilter($this->filters[$formField], $value);
                }

                $value = is_array($value)
                    ? Mage::helper('core')->jsonEncode($value)
                    : $value;

                $estimation->setData($dbField, $value);
            }
        }
        $estimation->save();

        return $estimation;
    }

    public function filterNumber($value)
    {
        return preg_replace('/\D/', '', $value);
    }

    public function filterAddons($addons)
    {
        foreach ($addons as $index => $addon) {
            if ('' === $addon) {
                unset($addons[$index]);
            }
        }

        return $addons;
    }

    protected function doFilter($filters, $value)
    {
        foreach ($filters as & $filter) {
            if ('self' === $filter[0]) {
                $filter[0] = $this;
            }
            $value = call_user_func($filter, $value);
        }

        return $value;
    }
}
