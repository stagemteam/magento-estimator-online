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
    protected $categories;

    protected function _toHtml()
    {
        //$this->setTemplate('followupemail/related.phtml');
        return $this->renderView();
    }

    public function getItems()
    {
        return $this->_productCollection;
    }

    public function getCategories()
    {
        if ($this->categories) {
            return $this->categories;
        }
        // @TODO Move id to system configuration
        $id = '58';
        $category = Mage::getModel('catalog/category')->load($id);
        if (is_object($category)) {
            // Create category collection for children
            $childrenCollection = $category->getCollection();
            // Only get child categories of parent cat
            $childrenCollection->addIdFilter($category->getChildren());
            // Only get active categories
            $childrenCollection->addAttributeToFilter('is_active', 1);
            // Add base attributes
            $childrenCollection->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('all_children')
                ->addAttributeToSelect('is_anchor')
                ->setOrder('position', Varien_Db_Select::SQL_ASC)
                ->joinUrlRewrite();
            // Add Image
            //$childrenCollection->addAttributeToSelect('image');
            return $this->categories = $childrenCollection;
        }
    }

    public function getProducts($category)
    {
        $productCollection = $category->getProductCollection();
        $productCollection
            ->addStoreFilter()
            ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addUrlRewrite();

        return $productCollection;
    }

    public function getSimpleProducts($configurable)
    {
        $children = Mage::getModel('catalog/product_type_configurable')->getUsedProductCollection($configurable);

        return $children->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
    }
}
