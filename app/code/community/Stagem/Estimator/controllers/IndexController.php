<?php

class Stagem_Estimator_IndexController extends Mage_Core_Controller_Front_Action
{
    public function getFormAction()
    {
        $content = $this->getLayout()->createBlock('core/template')->setTemplate('stagem/estimator/form.phtml')->toHtml();
        $this->getResponse()->setBody($content);
    }

    public function createAction()
    {
        /*$post = [
            'category_id' => 3,
            'manufacturer_id' => 19,
            'product_id' => 52,
            'addons' => [1 => 15, 3 => true],
            'email' => 'resipient@localhost.com'
        ];*/

        if ($post = $this->getRequest()->getPost()) {
            Mage::getSingleton('core/session')->setPostDataCall($post);

            /** @var Agere_Form_Helper_Data $helperData */
            $helperData = Mage::helper('agere_form');
            if (true || $helperData->checkCaptcha($post)) {
                try {
                    $filename = $helperData->fileDownload();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($this->__('You downloaded the wrong file'));

                    return Mage::app()->getResponse()->setRedirect(Mage::helper('core/http')->getHttpReferer());

                }
                $post['filename'] = $filename;
                /** @var Stagem_Estimator_Helper_Data $helperJob */
                $helperJob = Mage::helper('stagem_estimator');
                if ($helperJob->handleForm($post)) {
                    Mage::getSingleton('core/session')->addSuccess(
                        $this->__('Message has sent! The request will be processed shortly. Please, check your email.')
                    );
                    Mage::getSingleton('core/session')->unsPostDataCall();
                }
                $this->_redirect('');
            } else {
                Mage::getSingleton('core/session')->addError($this->__('You entered incorrectly captcha!'));

                return Mage::app()->getResponse()->setRedirect(Mage::helper('core/http')->getHttpReferer());
            }
        }
    }
}
