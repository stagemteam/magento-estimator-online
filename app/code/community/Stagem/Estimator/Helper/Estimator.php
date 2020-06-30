<?php

/**
 * @author Stagem Team
 * @copyright Copyright (c) 2020 Stagem (https://www.stagem.com.ua)
 * @package Stagem_Estimator
 */
class Stagem_Estimator_Helper_Estimator extends Mage_Core_Helper_Abstract
{
    /**
     * @param $product
     * @param Stagem_Estimator_Model_Addon[]|array $addons
     *
     * @return bool
     */
    public function getTotalPrice($product, $dataAddons)
    {
        if (!is_object($product)) {
            $product = Mage::getModel('catalog/product')->load($dataAddons['product_id']);
        }
        
        // Check if $addons are simple array with IDs
        //if (isset($addons[0]) && is_int($addons[0])) {
             $addons = Mage::getModel('stagem_estimator/addon')->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('id', ['in' => array_keys($dataAddons)]);
        //}
        //$addons->load(true);
        //echo $addons->getSelect();
        //die(__METHOD__);

        $total = $product->getPrice();
        foreach ($addons as $addon) {
            $total += $addon->calculate($dataAddons[$addon->getId()]);
        }
        
        return $total;
    }
    
}
