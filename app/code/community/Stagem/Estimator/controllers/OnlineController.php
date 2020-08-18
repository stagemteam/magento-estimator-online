<?php

class Stagem_Estimator_OnlineController extends Mage_Core_Controller_Front_Action
{
    public function getFormAction()
    {
        $content = $this->getLayout()->createBlock('core/template')->setTemplate('stagem/estimator/form.phtml')->toHtml();
        $this->getResponse()->setBody($content);
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function modifyAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function readyAction()
    {
        if (!($hash = Mage::app()->getRequest()->getParam('hash'))) {
            $this->_redirect('*/online/');
        }
        /** @var Stagem_Estimator_Model_Estimation $estimation */
        $estimation = Mage::getModel('stagem_estimator/estimation')->load($hash, 'hash');
        $estimation->setData('is_ready_to_install', 1)->save();
        /** @var Stagem_Estimator_Helper_Data $helperJob */
        $helperJob = Mage::helper('stagem_estimator');

        if ($helperJob->sendMail($estimation)) {
            //Mage::getSingleton('core/session')->addSuccess(
            //    $this->__('We\'ve got your confirmation for HVAC installation!')
            //);
            //Mage::getSingleton('core/session')->unsPostDataCall();
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function createAction()
    {
        if ($post = $this->getRequest()->getPost()) {
            Mage::getSingleton('core/session')->setPostDataCall($post);

            /** @var Agere_Form_Helper_Data $helperData */
            $helperData = Mage::helper('agere_form');
            if ($helperData->checkCaptcha($post)) {
                try {
                    $extension = array_map(
                        'trim', explode(',', Mage::getStoreConfig('stagem_estimator/general/file_extensions'))
                    );
                    $filenames = $helperData->fileDownload($extension);
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($this->__('You downloaded the wrong file!'));

                    return Mage::app()->getResponse()->setRedirect(Mage::helper('core/http')->getHttpReferer());
                }
                $post['files'] = $filenames;
                /** @var Stagem_Estimator_Helper_Data $helperJob */
                $helperJob = Mage::helper('stagem_estimator');
                if ($helperJob->handleForm($post)) {
                    Mage::getSingleton('core/session')->addSuccess(
                        $this->__('Message has sent! The request will be processed shortly. Please, check your email.')
                    );
                    Mage::getSingleton('core/session')->unsPostDataCall();
                }
                $this->_redirect('*/*/');
            } else {
                Mage::getSingleton('core/session')->addError($this->__('You entered incorrectly captcha!'));

                return Mage::app()->getResponse()->setRedirect(Mage::helper('core/http')->getHttpReferer());
            }
        }
    }
}
