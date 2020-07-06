<?php
/**
 * @category Stagem
 * @package Stagem_Estimator
 * @author Serhii Popov <popow.serhii@gmail.com>
 */
class Stagem_Estimator_Adminhtml_AddonController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		//die(__METHOD__);
		//$this->_title($this->__('Meta Tags'));
		// see layout
		//$this->_addContent($this->getLayout()->createBlock('popov_robots/adminhtml_robots'));

		$this->_initAction();
		$this->renderLayout();
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function editAction() {
		//$this->_initAction();

		$id = (int) $this->getRequest()->getParam('id');
		$model = Mage::getModel('stagem_estimator/addon')->load($id);

        // enable conditions
        //$model->getActions()->setJsFormObject('rule_actions_fieldset');

        //$this->loadLayout()->_setActiveMenu('stagem_estimator');


        $data = Mage::getSingleton('adminhtml/session')->getMetaData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register('current_stagem_addon', $model);

		$this->_initAction()
			->_addBreadcrumb($id ? $this->__('Edit Addon') : $this->__('New Addon'), $id ? $this->__('Edit Addon') : $this->__('New Addon'))
			//->_addContent($this->getLayout()->createBlock('popov_robots/adminhtml_robots_edit')->setData('action', $this->getUrl('*/*/save')))
			->renderLayout();
	}

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			try {
                if (isset($data['stores'])) {
                    if (in_array('0', $data['stores'])) {
                        $data['store_id'] = '0';
                    } else {
                        $data['store_id'] = implode(',', $data['stores']);
                    }
                    unset($data['stores']);
                }

				if (!$this->getRequest()->getParam('id')) {
					$data['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
					$data['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
				}

                /*if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                unset($data['rule']);*/

                /** @var Stagem_Estimator_Model_Addon $model */
                $model = Mage::getModel('stagem_estimator/addon');
                $mergedData = array_merge($model->getData(), $data);
                //$model->loadPost($data);
                $model->setData($mergedData)
                    ->setId($this->getRequest()->getParam('id'));
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Item was saved successfully'));
				Mage::getSingleton('adminhtml/session')->setMetaData(false);

				if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }

                $this->_redirect('*/*/');
			} catch (Mage_Core_Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this item'));
			}

			return;
		}

		Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}

	public function copyAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        try {
            $model = Mage::getModel('stagem_estimator/addon')->load($id);

            $data = $model->getData();
            $data['id'] = null;
            $data['is_active'] = 0;
            $data['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $data['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');

            $copy = Mage::getModel('stagem_estimator/addon');
            $copy->setData($data);
            $copy->save();
            $id = $copy->getId();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Item was copied successfully'));
            Mage::getSingleton('adminhtml/session')->setMetaData(false);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/edit', array('id' => $id));
    }

	public function deleteAction() {
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				Mage::getModel('stagem_estimator/addon')->setId($id)->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Addon was deleted successfully'));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
			}
		}
		$this->_redirect('*/*/');
	}

	/**
	 * Initialize action
	 *
	 * Here, we set the breadcrumbs and the active menu
	 *
	 * @return Mage_Adminhtml_Controller_Action
	 */
	protected function _initAction() {
		$this->loadLayout()
			// Make the active menu match the menu config nodes (without 'children' inbetween)
			->_setActiveMenu('stagem_estimator/stagem_estimator_meta')
			->_title($this->__('Estimator'))->_title($this->__('Addons'))
			->_addBreadcrumb($this->__('Estimator'), $this->__('Estimator'))
			->_addBreadcrumb($this->__('Addons'), $this->__('Addons'));

		return $this;
	}

	/**
	 * Check currently called action by permissions for current user
	 *
	 * @return bool
	 */
	protected function _isAllowed()	{
		return Mage::getSingleton('admin/session')->isAllowed('stagem_estimator/addon');
	}

}
