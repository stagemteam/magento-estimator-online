<?php

/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */
class Stagem_Estimator_Model_Estimation extends Mage_CatalogRule_Model_Rule
{
    protected function _construct()
    {
        $this->_init('stagem_estimator/estimation');
    }

    /**
     * @param $product
     * @param Stagem_Estimator_Model_Addon[]|array $addons
     *
     * @return float
     */
    public function getTotalPrice()
    {
        $product = $this->getProduct();
        $addons = $this->getAddons();

        //$addons->load(true);
        //echo $addons->getSelect();
        //die(__METHOD__);

        $selectedAddons = $this->getSelectedAddons();

        $total = $product->getPrice();
        foreach ($addons as $addon) {
            $total += $addon->calculate($selectedAddons[$addon->getId()]);
        }

        return $total;
    }

    /**
     * @param Stagem_Estimator_Model_Addon $addon
     */
    public function calculate($addon)
    {
        $selectedAddons = $this->getSelectedAddons();

        $price = $addon->calculate($selectedAddons[$addon->getId()]);

        return $price;
    }

    public function getFiles()
    {
        return Mage::helper('core')->jsonDecode($this->getData('files'));
    }
    
    /**
     * @return array
     */
    public function getSelectedAddons()
    {
        return Mage::helper('core')->jsonDecode($this->getData('addons'));
    }

    /**
     * @return array
     */
    public function getCustomer()
    {
        return Mage::helper('core')->jsonDecode($this->getData('customer'));
    }
    
    public function getCustomerValue($attr)
    {
        $value = $this->getCustomer()[$attr];
        
        return $value;
    }

    public function getProduct()
    {
        return Mage::getModel('catalog/product')->load($this->getProductId());
    }

    public function getConfigurable()
    {
        return Mage::getModel('catalog/product')->load($this->getConfigurableId());
    }

    /**
     * @return Stagem_Estimator_Model_Addon[]
     */
    public function getAddons()
    {
        $addonIds = array_keys($selectedAddons = $this->getSelectedAddons());
        $addons = Mage::getModel('stagem_estimator/addon')->getCollection()
            ->addFieldToFilter('id', ['in' => $addonIds])
            ->setOrder('priority', 'ASC')
        ;

        return $addons;
    }
}
