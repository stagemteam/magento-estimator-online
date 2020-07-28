<?php
/**
 * @category Popov
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */

class Stagem_Estimator_Model_System_Config_Manufacturer
{
    protected function getManufacturers()
    {
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode('catalog_product', 'manufacturer');

        return $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getData('attribute_id'))
            ->setStoreFilter(0, false);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $valuesCollection = $this->getManufacturers();

        $options = [];
        foreach($valuesCollection as $value) {
            $options[] = ['value' => $value->getOptionId(), 'label' => $value->getValue()];
        }

        return $options;
    }
}
