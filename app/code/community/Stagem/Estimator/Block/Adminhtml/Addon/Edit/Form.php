<?php

/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 26.06.15 15:14
 */
class Stagem_Estimator_Block_Adminhtml_Addon_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('stagem_addon_form'); //stagem_estimator_meta_form
        $this->setTitle($this->__('Addon Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    /*protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }*/

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', [
                'id' => $this->getRequest()->getParam('id'),
            ]),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
