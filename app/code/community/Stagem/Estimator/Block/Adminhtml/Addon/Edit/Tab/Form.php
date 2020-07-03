<?php
/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */
class Stagem_Estimator_Block_Adminhtml_Addon_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
		$model = Mage::registry('current_stagem_addon');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => $this->__('Addon Info'),
			'class' => 'fieldset-wide',
		));

		if ($model->getId()) {
			$fieldset->addField('id', 'hidden', array(
				'name' => 'id',
			));
		}

		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_id', 'multiselect', array(
				'name' => 'stores[]',
				'label' => $this->__('Store View'),
				'title' => $this->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));
		} else {
			$fieldset->addField('store_id', 'hidden', array(
				'name' => 'stores[]',
				'value' => Mage::app()->getStore(true)->getId(),
			));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
		}
		
		/*$fieldset->addField('category_id', 'select', array(
			'label'    => $this->__('Category'),
			'name'     => 'category_id',
			'required' => true,
			'value'    => $model->getCategoryId(),
			'values'   => Mage::getModel('Stagem_Estimator_Model_System_Config_Category')->toOptionArray(),
		));*/

		$fieldset->addField('type', 'select', array(
			'label'     => $this->__('Type'),
			'name'      => 'type',
			'required'  => true,
            'values'   => Mage::getModel('stagem_estimator/system_config_type')->toOptionArray(),
		));

        $fieldset->addField('name', 'text', array(
            'label'              => $this->__('Name'),
            'name'               => 'name',
            'required'           => true,
            'style'              => 'width:100%',
            //'after_element_html' => '<small>Example: category;manufacturer</small>',
        ));

		#$fieldset->addField('context', 'select', array(
		#	'label'     => $this->__('Context'),
		#	'name'      => 'context',
		#	'required'  => true,
		#	'options'   => Mage::getModel('Stagem_Estimator_Model_System_Config_Context')->toOptionArray(),
		#));

        $fieldset->addField('price_condition', 'textarea', array(
            'name'     => 'price_condition',
            'label'    => $this->__('Price Condition'),
            'title'    => $this->__('Price Condition'),
            'required' => false,
            'style'    => 'width: 100%; height: 200px;',
            'after_element_html' => '<small>
<strong>input (default)</strong><br/>
Price condition: :pricePerUnit
<br/>
<br/>

Input (з кореляцією) 
----
**Rule Name: ** Ведіть довжину комунікацій
**Type**: input
**Price condition**: :$pricePerMeter | $meter = $feets
Example:
15 | 1 = 3
**Round to a larger number**: Yes/No

6 feet / 3 feet one piece = 2 pieces

7 / 3 =  2,3 = 3 (заокруглюємо в більшу сторону) 

----
checkbox
----
**Rule Name: ** Монтаж проводиться на горищі? 
**Type**: checkbox
**Price condition**: :$price

----
select/checkbox/radiobutton
----
**Rule Name: ** Введіть тип будинку 
**Type**: select/checkbox/radiobutton
**Price condition**: $name: $price
Example:
Бунгало: 100
2-поверховий: 200


----
input-select
----
**Rule Name: ** Введіть суму в UAH і виберіть валюту покупки 
**Type**: input/select
**Price condition**: $name: | $from = $to
Example:
USD | 1 = 26
EUR | 1 = 29
</small>',
        ));

		$fieldset->addField('free', 'text', array(
			'label'     => $this->__('Free amount of units'),
			'name'      => 'round_up',
            'required'  => false,
            'style'     => 'width:20%',
		));

		$fieldset->addField('round_up', 'select', array(
			'label'     => $this->__('Round up to higher number'),
			'name'      => 'round_up',
			'required'  => true,
			'options'   => array(
				1 => $this->__('Yes'),
				0 => $this->__('No'),
			),
		));

		$fieldset->addField('is_active', 'select', array(
			'label'     => $this->__('Status'),
			'name'      => 'is_active',
			'required'  => true,
			'options'   => array(
				1 => $this->__('Enabled'),
				0 => $this->__('Disabled'),
			),
		));

        $fieldset->addField('placeholder', 'text', array(
            'name'     => 'placeholder',
            'label'    => $this->__('Placeholder'),
            'title'    => $this->__('Placeholder'),
            'required' => false,
            'after_element_html' => '<small></small>'
        ));

        $fieldset->addField('comment', 'textarea', array(
            'name'     => 'Comment',
            'label'    => $this->__('Comment'),
            'title'    => $this->__('Comment'),
            'required' => false,
            'style'    => 'width: 100%; height: 200px;',
            'after_element_html' => '<small>Information which help a user enter or select correct data.</small>'
        ));

		$fieldset->addField('priority', 'text', array(
			'label'     => $this->__('Priority'),
			'name'      => 'priority',
			'required'  => false,
            'style'     => 'width:20%',
            'after_element_html' => '<small>Higher priority means the addon is printed first. By default, the first attached addon is read.</small>',
		));

		$fieldset->addField('created_at', 'date', array(
			'label'    => $this->__('Created at'),
			'name'     => 'created_at',
			'required' => false,
			'readonly' => true,
			'disabled' => true,
			'format'   => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
			'time'     => true,
		));

		$fieldset->addField('updated_at', 'date', array(
			'label'    => $this->__('Updated at'),
			'name'     => 'updated_at',
			'required' => false,
			'readonly' => true,
			'disabled' => false,
			'format'   => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
			'time'     => true,
		));

		// this must be after fieldset declaration
        if ($data = Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues($data);
        } else {
            $form->setValues($model->getData());
        }
        #$form->setUseContainer(true);
        #$this->setForm($form);


        return parent::_prepareForm();
	}

}
