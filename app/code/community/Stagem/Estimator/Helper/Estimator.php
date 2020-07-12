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
        'phone'=> [
            ['self', 'filterNumber']
        ]
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
                $value = is_array($data[$formField])
                    ? Mage::helper('core')->jsonEncode($data[$formField])
                    : $data[$formField];

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

}
