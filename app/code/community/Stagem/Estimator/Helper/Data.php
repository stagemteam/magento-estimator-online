<?php

/**
 * @author Stagem Team
 * @copyright Copyright (c) 2020 Stagem (https://www.stagem.com.ua)
 * @package Stagem_Estimator
 */
class Stagem_Estimator_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SECTION_NAME = 'stagem_estimator';

    public function handleForm($data)
    {
        /** @var Agere_Form_Helper_Data $dataHelper */
        $dataHelper = Mage::helper('agere_form');
        
        /** @var Stagem_Estimator_Helper_Estimator $estimatorHelper */
        $estimatorHelper = Mage::helper('stagem_estimator/estimator');

        $data['total_price'] = $estimatorHelper->getTotalPrice($data['product_id'], $data['addons']);


        //$data['subject'] = $this->getDefaultCategory($data['contact-id'])->getName();

        return $dataHelper->sendSimpleMail($data, Stagem_Estimator_Helper_Data::SECTION_NAME, $data['email']);
    }
    
}
