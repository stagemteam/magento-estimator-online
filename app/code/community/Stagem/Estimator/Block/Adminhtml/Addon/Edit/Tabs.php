<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2020 Stagem Team
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

class Stagem_Estimator_Block_Adminhtml_Addon_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stagem_addon_tabs');
        $this->setDestElementId('edit_form'); //this should be same as the form id define above
        //$this->setTitle(Mage::helper('form')->__('Rule Information')); // use $this instead of
        $this->setTitle($this->__('Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('stagem_addon_form', array(
            'label'     => $this->__('General'),
            'title'     => $this->__('General'),
            'content'   => $this->getLayout()->createBlock('stagem_estimator/adminhtml_addon_edit_tab_form')->toHtml(),
        ));

        /*$this->addTab('stagem_addon_conditions', array(
            'label'     => $this->__('Conditions'),
            'title'     => $this->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('stagem_estimator/adminhtml_meta_edit_tab_conditions')->toHtml(),
        ));*/

        return parent::_beforeToHtml();
    }
}
