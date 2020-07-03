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

    protected $data = [
        'categories' => [],
        'brands' => [],
        'configurables' => [],
        'products' => [],
        'addons' => [],
    ];

    protected function _construct()
    {
        $this->getnerateData();
        parent::_construct();
    }

    protected function _toHtml()
    {
        //$this->setTemplate('followupemail/related.phtml');
        return $this->renderView();
    }
    
    public function getFormUrl()
    {
        return Mage::getUrl('estimator/index/create');
    }

    public function getnerateData()
    {
        foreach ($this->getCategories() as $category) {
            $this->data['categories'][] = [
                'id' => $category->getId(),
                'value' => $category->getId(),
                'label' => $category->getName(),
            ];

            $brands = [];
            foreach ($this->getConfigurables($category) as $configurable) {
                $this->data['configurables'][] = [
                    'id' => $configurable->getId(),
                    'value' => $configurable->getId(),
                    'brandId' => $configurable->getManufacturer(),
                    'label' => $configurable->getName(),
                ];

                $this->data['brands'][$configurable->getManufacturer()] = [
                    'id' => $configurable->getManufacturer(),
                    'categoryId' => $category->getId(),
                    'value' => $configurable->getManufacturer(),
                    'label' => $configurable->getAttributeText('manufacturer'),
                ];

                foreach ($this->getProducts($configurable) as $simple) {
                    $this->data['powers'][] = [
                        'id' => $simple->getId(),
                        'modelId' => $configurable->getId(),
                        'value' => $simple->getId(),
                        'label' => $simple->getName(),
                    ];
                }
            }
            $this->data['brands'] = array_values($this->data['brands']);
        }
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

    public function getConfigurables($category)
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

    public function getProducts($configurable)
    {
        $children = Mage::getModel('catalog/product_type_configurable')->getUsedProductCollection($configurable);

        return $children->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
    }

    public function categoriesToJson()
    {
        return Mage::helper('core')->jsonEncode($this->data['categories']);
    }

    public function manufacturersToJson()
    {
        return Mage::helper('core')->jsonEncode($this->data['brands']);
    }

    public function configurablesToJson()
    {
        return Mage::helper('core')->jsonEncode($this->data['configurables']);
    }

    public function powersToJson()
    {
        return Mage::helper('core')->jsonEncode($this->data['powers']);
    }

    public function addonsToJson()
    {
        $addons = Mage::getModel('stagem_estimator/addon')->getCollection()
            ->addFieldToFilter('is_active', 1);

        $data = [
            'separated' => [],
            'grouped' => [],
        ];
        /** @var Stagem_Estimator_Model_Addon $addon */
        foreach ($addons as $addon) {
            $element = [
                'id' => $addon->getId(),
                'order' => $addon->getPriority(),
                'type' => $addon->getType(),
                'value' => $addon->getValue(),
                'label' => $addon->getName(),
                'title' => $addon->getName(),
                'order' => $addon->getPriority(),
                'name' => 'addons[' . $addon->getId() . ']',
                'isSeparate' => $addon->isSeparate(),
                'isMultiple' => $addon->isMultiple(),
                'infoMessage' => $addon->getInfoMessage(),
                'placeholder' => $addon->getPlaceholder(),
            ];

            $addon->isSeparate()
                ? array_push($data['separated'], $element)
                : array_push($data['grouped'], $element);
        }

        return Mage::helper('core')->jsonEncode($data);
    }
}
