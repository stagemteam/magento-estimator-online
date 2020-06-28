<?php

/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */
class Stagem_Estimator_Block_Adminhtml_Addon_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    protected function _construct()
    {
        $this->_blockGroup = 'stagem_estimator';
        $this->_controller = 'adminhtml_addon';
        $this->setData('action', $this->getUrl('*/*/save'));

        $this->_addButton('saveandcontinue', array(
            'label'     => $this->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_addButton('copy', array(
            'label' => Mage::helper('catalog')->__('Copy'),
            'onclick' => "setLocation('{$this->getCopyUrl()}')",
            'class' => 'copy',
        ), 1);

        $this->_formScripts[] = "function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }";
    }

    protected function getCopyUrl()
    {
        return $this->getUrl('*/*/copy', array(
            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
            Mage_Core_Model_Url::FORM_KEY => $this->getFormKey(),
        ));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = Mage::registry('current_stagem_addon');
        if ($model->getId()) {
            return $this->__("Edit Addon");
        } else {
            return $this->__("Add Addon");
        }
    }
}
