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
        'manufacturers' => [],
        'configurables' => [],
        'products' => [],
        'addons' => [],
    ];

    protected function _construct()
    {
        $this->generateData();
        parent::_construct();
    }

    protected function _toHtml()
    {
        if (!Mage::getStoreConfig('stagem_estimator/general/enabled')) {
            return null;
        }
            
        //$this->setTemplate('followupemail/related.phtml');
        return $this->renderView();
    }
  
    public function getStoreLang()
    {
        return substr(Mage::getStoreConfig('general/locale/code'), 0, 2);    
    }
    
    public function getFormUrl()
    {
        return Mage::getUrl('estimator/online/create');
    }

    
    public function getMediaUrl($path)
    {
        $url = null;
        if (!empty($path)) {
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $path;
        }

        return $url;
    }

    /**
     * Retrieve thumbnail image URL
     *
     * @param $category
     * @return string
     */
    public function getCategoryThumbnailImageUrl($category)
    {

        $url = null;
        if ($image = $category->getThumbnail()) {
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/category/' . $image;
        }

        return $url;
    }

    public function generateData()
    {
        foreach ($this->getCategories() as $category) {
            $this->data['categories'][] = [
                'id' => $category->getId(),
                'value' => $category->getId(),
                'label' => $category->getName(),
                'image' => $this->getCategoryThumbnailImageUrl($category),
            ];

            $brands = [];
            foreach ($this->getConfigurables($category) as $configurable) {
                $this->data['configurables'][] = [
                    'id' => $configurable->getId(),
                    'value' => $configurable->getId(),
                    'brandId' => $configurable->getManufacturer(),
                    'label' => $configurable->getName(),
                ];

                $this->data['manufacturers'][$configurable->getManufacturer()] = [
                    'id' => $configurable->getManufacturer(),
                    'categoryId' => $category->getId(),
                    'value' => $configurable->getManufacturer(),
                    'label' => $configurable->getAttributeText('manufacturer'),
                ];

                foreach ($this->getProducts($configurable) as $simple) {
                    $this->data['products'][] = [
                        'id' => $simple->getId(),
                        'modelId' => $configurable->getId(),
                        'value' => $simple->getId(),
                        'label' => $simple->getAttributeText('cooling_capacity'), // @TODO Generate label based on the pattern from System config
                    ];
                }
            }
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
                ->addAttributeToSelect('thumbnail')
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

    /**
     * @return Bc_Manufacturer_Model_Mysql4_Manufacturer_Collection
     */
    public function getManufacturers()
    {
        $collection = Mage::getModel('manufacturer/manufacturer')
            ->getCollection()
            ->addFieldToFilter('status', 1)
        ;

        return $collection;
    }

    public function categoriesToJson()
    {
        return Mage::helper('core')->jsonEncode($this->data['categories']);
    }

    public function manufacturersToJson()
    {
        $manufacturers = $this->getManufacturers();
        /** @var Bc_Manufacturer_Model_Manufacturer $manufacturer */
        foreach ($manufacturers as $id => $manufacturer) {
            $option = & $this->data['manufacturers'][$manufacturer->getOptionId()];

            $option['logo'] = $this->getMediaUrl($manufacturer->getLogoWebPath());
            $option['generalImage'] = $this->getMediaUrl($manufacturer->getGeneralImage());
        }

        return Mage::helper('core')->jsonEncode(array_values($this->data['manufacturers']));
    }

    public function configurablesToJson()
    {
        return Mage::helper('core')->jsonEncode($this->data['configurables']);
    }

    public function productsToJson()
    {
        return Mage::helper('core')->jsonEncode($this->data['products']);
    }

    public function addonsToJson()
    {
        $addons = Mage::getModel('stagem_estimator/addon')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->setOrder('priority', 'ASC');

        $data = [
            'separated' => [],
            'grouped' => [],
        ];
        /** @var Stagem_Estimator_Model_Addon $addon */
        foreach ($addons as $addon) {
            $element = [
                'id' => $addon->getId(),
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

    public function estimationToJson()
    {
        $hash = Mage::app()->getRequest()->getParam('hash');
        if (!$hash) {
            return Mage::helper('core')->jsonEncode(null);
        }

        /** @var Stagem_Estimator_Model_Estimation $estimation */
        $estimation = Mage::getModel('stagem_estimator/estimation')->load($hash, 'hash');

        $data = $estimation->getData();
        $data['addons'] = $estimation->getSelectedAddons();
        $data['customer'] = $estimation->getCustomer();
        $data['files'] = $estimation->getFiles();
        $json = Mage::helper('core')->jsonEncode($data);

        return $json;
    }
}
