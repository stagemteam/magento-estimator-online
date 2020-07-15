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

        $fieldset->addField('is_separate', 'select', array(
            'label'     => $this->__('Show addon in a separate block'),
            'name'      => 'is_separate',
            'required'  => false,
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

        $fieldset->addField('price_condition', 'textarea', array(
            'name'     => 'price_condition',
            'label'    => $this->__('Price Condition'),
            'title'    => $this->__('Price Condition'),
            'required' => false,
            'style'    => 'width: 100%; height: 200px;',
            'after_element_html' => <<<HTML
<small>
<h3>Price Condition formats</h3> 

NOTE: percentage (%) sign at the beginning of <i>%price</i>, <i>%from</i> etc. means you should place <b>number</b> there. 
<br/>
<br/>

<h5><i>(empty)</i></h5>
If you leave price condition empty, a user will see <i>Variable</i> value in the calculation, 
that means a addon cannot be calculated automatically and requires human participation.
<br/>
<br/>

<h5><strong>%price</strong></h5>
If you write just <b>%price</b> in price condition, the value entered by a user will be multiplied by <b>%price</b> value.
<br/>
Example: %price = 10. The user enters <i>6</i> in the input field on the frontend part, 
then the formula will have the next format: 6 * 10 = 60.
<br/>
He will see <i>60</i> as the result of calculation. 
<br/>
<br/>

<h5><strong>%price | feet</strong></h5>
If you write <b>%price | feet</b> in price condition, this will work exectly the same as explanation for the <b>%price</b>,
except a user will see a unit of measurement in the calculation.
<br/>
Example: 10 | feet. The user enters <i>6</i> in the input field on the frontend part.
<br/>
He will see <i>6 feet = 60</i> as the result of calculation.
<br/>
<br/>

<h5><strong>%price | %from piece = %to feet</strong></h5>
If you write <b>%price | %from piece = %to feet</b> in price condition, 
this mean you want to convert a value entered by a user <i>%from</i> one unit <i>%to</i> another.
<br/>
Example: 10 | 1 piece = 4 feet. The user enters <i>6</i> in the input field on the frontend part,
then the formula will have the next format: (6 / 4) * 10 = 15.
<br/>
He will see <i>6 feet = 15</i> as the result of calculation.
<br/>
NOTE: If you enable <i>Round Up</i> option, then the result of (6 / 4) = 1.5 will be converted to higher number 2,
and final the result will be 20.
<br/>
Example: 1 | 1 USD = 25 UAH. The user enters <i>60</i> in the input field on the frontend part,
then the formula will have the next format: (60 / 25) * 1 = 2.4.
<br/>
<br/>
<br/>


<h3>Multiple Types</h3>
Types <b>select/checkbox/radio</b> can have multiple price conditions. Each condition should be placed at a new line.
<br/>
Example:
<br/>
0 | Bungalow<br/>
0 | Two storey apartment<br/>
0 | Two storey house<br/>
0 | Condo<br/>

</small>
HTML
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
