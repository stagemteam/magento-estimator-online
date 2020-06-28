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
				//$data = $this->_filterDates($data, array('updated_at'));

                if (isset($data['stores'])) {
                    if (in_array('0', $data['stores'])) {
                        $data['store_id'] = '0';
                    } else {
                        $data['store_id'] = implode(',', $data['stores']);
                    }
                    unset($data['stores']);
                }

				/** @var Stagem_Estimator_Model_MetaTag_Factory $factory */
				$factory = Mage::getModel('stagem_estimator/metaTag_factory');

				$data['seo_attributes'] = implode(';', $factory->create($data['type'])->handleSeoAttributes($data['seo_attributes']));
				$data['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
				if (!$this->getRequest()->getParam('id')) {
					$data['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
				}

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                //if (isset($data['rule']['actions'])) {
                //    $data['actions'] = $data['rule']['actions'];
                //}
                unset($data['rule']);
                //$model->loadPost($data);

                /** @var Stagem_Estimator_Model_Addon $model */
				$model = Mage::getModel('stagem_estimator/rule');
				$model->setData($data)
                //->setId($this->getRequest()->getParam('id'));
                /*$model*/->loadPost($data)
                    ->setId($this->getRequest()->getParam('id'));
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('SEO Rule was saved successfully'));
				Mage::getSingleton('adminhtml/session')->setMetaData(false);

				if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }

                $this->_redirect('*/*/');
			} catch (Mage_Core_Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this SEO Rule'));
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
            $model = Mage::getModel('stagem_estimator/rule')->load($id);

            $data = $model->getData();
            $data['id'] = null;
            $data['is_active'] = 0;
            $data['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $data['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');

            $copy = Mage::getModel('stagem_estimator/rule');
            $copy->setData($data);
            $copy->save();
            $id = $copy->getId();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('SEO Rule was copied successfully'));
            Mage::getSingleton('adminhtml/session')->setMetaData(false);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/edit', array('id' => $id));
    }

	public function deleteAction() {
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				Mage::getModel('stagem_estimator/meta')->setId($id)->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('SEO Rule was deleted successfully'));
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
			->_title($this->__('SEO'))->_title($this->__('Meta Tags'))
			->_addBreadcrumb($this->__('SEO'), $this->__('SEO'))
			->_addBreadcrumb($this->__('Meta Tags'), $this->__('Meta Tags'));

		return $this;
	}

	/**
	 * Check currently called action by permissions for current user
	 *
	 * @return bool
	 */
	protected function _isAllowed()	{
		return Mage::getSingleton('admin/session')->isAllowed('stagem_estimator/stagem_estimator_addon');
	}

}
