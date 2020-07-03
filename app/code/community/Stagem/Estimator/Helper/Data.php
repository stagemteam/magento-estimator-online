<?php

/**
 * @author Stagem Team
 * @copyright Copyright (c) 2020 Stagem (https://www.stagem.com.ua)
 * @package Stagem_Estimator
 */
class Stagem_Estimator_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SYSTEM_SECTION_NAME = 'stagem_estimator';

    public function handleForm($data)
    {
        /** @var Agere_Form_Helper_Data $dataHelper */
        $dataHelper = Mage::helper('agere_form');
        
        /** @var Stagem_Estimator_Helper_Estimator $estimatorHelper */
        $estimatorHelper = Mage::helper('stagem_estimator/estimator');

        $estimation = $estimatorHelper->populateFormData($data);

        //$data['total_price'] = $estimation->getTotalPrice();

        $vars = [];
        $vars['subject'] = 'Online Estimator evaluation';
        $vars['estimation'] = $estimation;
        $vars['phones'] = explode(',', Mage::getStoreConfig('general/store_information/phone'));

        return $dataHelper->sendSimpleMail(
            $vars,
            Stagem_Estimator_Helper_Data::SYSTEM_SECTION_NAME,
            $data['customer']['email']
        );
    }

}
