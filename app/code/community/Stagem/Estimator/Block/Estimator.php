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
                'id' => (int) $category->getId(),
                'value' => (int) $category->getId(),
                'label' => $category->getName(),
                'image' => $this->getCategoryThumbnailImageUrl($category),
            ];

            foreach ($this->getConfigurables($category) as $configurable) {
                $this->data['configurables'][] = [
                    'id' => (int) $configurable->getId(),
                    'value' => (int) $configurable->getId(),
                    'label' => $configurable->getName(),
                    'categoryId' => (int) $category->getId(),
                    'brandId' => (int) $configurable->getManufacturer(),
                ];

                $manufacturer = $this->data['manufacturers'][$configurable->getManufacturer()] ?? [];
                $manufacturer['id'] = (int) $configurable->getManufacturer();
                isset($manufacturer['categoryIds'])
                    ? array_push($manufacturer['categoryIds'], (int) $category->getId())
                    : $manufacturer['categoryIds'] = [(int) $category->getId()];
                $manufacturer['value'] = (int) $configurable->getManufacturer();
                $manufacturer['label'] = $configurable->getAttributeText('manufacturer');
                $this->data['manufacturers'][$configurable->getManufacturer()] = $manufacturer ;

                foreach ($this->getProducts($configurable) as $simple) {
                    $this->data['products'][] = [
                        'id' => (int) $simple->getId(),
                        'modelId' => (int) $configurable->getId(),
                        'value' => (int) $simple->getId(),
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
            ->addAttributeToFilter('manufacturer', ['nin' =>
                explode(',', Mage::getStoreConfig('stagem_estimator/general/ignored_brands'))
            ])
            //->addAttributeToFilter('qty', ['gt' => 0])
            ->addUrlRewrite();

        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);

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
            if (isset($this->data['manufacturers'][$manufacturer->getOptionId()])) {
                $option = &$this->data['manufacturers'][$manufacturer->getOptionId()];
                $option['logo'] = $this->getMediaUrl($manufacturer->getLogoWebPath());
                $option['generalImage'] = $this->getMediaUrl($manufacturer->getGeneralImage());
                $option['categoryIds'] = array_values(array_unique($option['categoryIds']));
            }
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
                'id' => (int) $addon->getId(),
                'type' => $addon->getType(),
                'value' => $this->getAddonValue($addon),
                'label' => $this->__($addon->getName()),
                // We use helper for translate placeholder to avoid broken frontend when Translate Inline is enabled
                'title' => Mage::helper('stagem_estimator')->__($addon->getName()),
                'order' => $addon->getPriority(),
                'name' => 'addons[' . $addon->getId() . ']',
                'isSeparate' => $addon->isSeparate(),
                'isMultiple' => $addon->isMultiple(),
                'infoMessage' => $this->__($addon->getInfoMessage()),
                'placeholder' => Mage::helper('stagem_estimator')->__($addon->getPlaceholder()),
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

    /**
     * Prepare value for form element.
     *
     * It returns addon ID if there is no variations, otherwise array return.
     *
     * @param Stagem_Estimator_Model_Addon $addon
     * @return int|array
     */
    public function getAddonValue($addon)
    {
        $value = $addon->getId();

        if ($addon->isMultiple()) {
            $value = [];
            $conditions = $addon->parsePriceConditions();
            foreach ($conditions as $index => $condition) {
                $value[] = [
                    'value' => $index,
                    'label' => isset($condition['to_unit'])
                        ? $this->__($condition['from_unit']) . ' - ' . $this->__($condition['to_unit'])
                        : $this->__($condition['from_unit']),
                ];
            }
        }

        return $value;
    }
}
