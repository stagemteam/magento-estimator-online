<?php
/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */
class Stagem_Estimator_Block_Adminhtml_Addon extends Mage_Adminhtml_Block_Widget_Grid_Container {

	/**
	 * How to correct name an admin block @link http://stackoverflow.com/a/5716697/1335142
	 */
	protected function _construct() {
		$this->_blockGroup = 'stagem_estimator';
		$this->_controller = 'adminhtml_addon';
		$this->_headerText = $this->__('Addons');
		$this->_addButtonLabel = $this->__('Add');

		parent::_construct();
	}

}
