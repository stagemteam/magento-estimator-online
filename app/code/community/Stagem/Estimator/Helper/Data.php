<?php

/**
 * @author Stagem Team
 * @copyright Copyright (c) 2020 Stagem (https://www.stagem.com.ua)
 * @package Stagem_Estimator
 */
class Stagem_Estimator_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SYSTEM_SECTION_NAME = 'stagem_estimator';

    public function __()
    {
        $translator = Mage::app()->getTranslator();
        $default = $translator->getTranslateInline();
        $translator->setTranslateInline(false);

        $translated = $translator->translate(func_get_args());

        $translator->setTranslateInline($default);

        return $translated;
    }

    public function getTranslateInline()
    {
        // Disable Translate Inline though helper to avoid broken frontend
        return false;
    }

    public function handleForm($data)
    {
        /** @var Agere_Form_Helper_Data $dataHelper */
        $dataHelper = Mage::helper('agere_form');
        /** @var Stagem_Estimator_Helper_Estimator $estimatorHelper */
        $estimatorHelper = Mage::helper('stagem_estimator/estimator');
        $estimation = $estimatorHelper->populateFormData($data);

        return $this->sendMail($estimation, $estimation->getCustomerValue('email'));
    }

    /**
     * @param Stagem_Estimator_Model_Estimation $estimation
     * @param string $sendTo
     *
     * @return bool
     */
    public function sendMail($estimation, $sendTo = null)
    {
        $action = Mage::app()->getRequest()->getActionName();
        $subjects = [
            'default' => 'Online HVAC Estimate',
            'ready' => 'Ready to Install'
        ];
        $subject = $subjects[$action] ?? $subjects['default'];

        /** @var Agere_Form_Helper_Data $dataHelper */
        $dataHelper = Mage::helper('agere_form');

        $vars = [];
        $vars['estimation'] = $estimation;
        $vars['files'] = $estimation->getFiles();
        $vars['subject'] = $this->__($subject) . ' | ' . Mage::getStoreConfig('general/store_information/name');
        $vars['phones'] = array_filter(explode(',', Mage::getStoreConfig('general/store_information/phone')));
        $vars['logo_src'] = Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));

        return $dataHelper->sendSimpleMail(
            $vars,
            Stagem_Estimator_Helper_Data::SYSTEM_SECTION_NAME,
            $sendTo
        );
    }
}
