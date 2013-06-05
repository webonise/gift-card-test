<?php

class Webtex_Giftcards_Model_Product_Type_Giftcards extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Initialize gift card for add to cart process
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return array|string
    */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $product = $this->getProduct($product);
        $data = $buyRequest->getData();
        $cardType = $product->getAttributeText('wts_gc_type');

        //change delivery date to mysql format
        if(!empty($data['mail_delivery_date'])) {
            $tempDate = explode('/', $data['mail_delivery_date']);
            $mailDeliveryDate = $tempDate[2].'-'.$tempDate[0].'-'.$tempDate[1];
        } else {
          $mailDeliveryDate = null;
        }


        /*
         * Validate card amount
         * TODO: Need options validation
         */
        if (!$product->getPrice()) {
            // true only if min value is set (more than 0) and price less than min
            $min = Mage::getStoreConfig('giftcards/default/min_card_value') > 0 && $data['card_amount'] < Mage::getStoreConfig('giftcards/default/min_card_value');
            // true only if max value is set (more than 0) and price more than max
            $max = Mage::getStoreConfig('giftcards/default/max_card_value') > 0 && $data['card_amount'] > Mage::getStoreConfig('giftcards/default/max_card_value');
            // if one of conditions above is true than return error
            if ($min || $max) {
                return $this->getSpecifyPriceMessage();
            }
        }

        /*
         * Validate card type
         * TODO: Need options validation
         */
       /* if (!isset($data['card_type']) || !in_array($data['card_type'], array('email', 'print', 'offline'))) {
            return $this->getSpecifyOptionsMessage();
        }*/

        /*
         * Add gift card params as product custom options to product quote
         * TODO: Need options validation
         */
        $product->addCustomOption('card_type', $cardType);
        $product->addCustomOption('card_amount', $data['card_amount']);
        $product->addCustomOption('card_currency', Mage::app()->getStore()->getCurrentCurrencyCode());
        $product->addCustomOption('mail_to', $data['mail_to']);
        $product->addCustomOption('mail_to_email', $data['mail_to_email']);
        $product->addCustomOption('mail_from', $data['mail_from']);
        $product->addCustomOption('mail_message', $data['mail_message']);
        $product->addCustomOption('offline_country', $data['offline_country']);
        $product->addCustomOption('offline_state', $data['offline_state']);
        $product->addCustomOption('offline_city', $data['offline_city']);
        $product->addCustomOption('offline_street', $data['offline_street']);
        $product->addCustomOption('offline_zip', $data['offline_zip']);
        $product->addCustomOption('offline_phone', $data['offline_phone']);
        $product->addCustomOption('mail_delivery_date', $mailDeliveryDate);//delivery date of email

        return parent::_prepareProduct($buyRequest, $product, $processMode);
    }

    public function getSpecifyOptionsMessage()
    {
        return Mage::helper('catalog')->__('Please specify the product\'s option(s).');
    }

    public function getSpecifyPriceMessage()
    {
        return Mage::helper('giftcards')->__('Card amount is not within the specified range.');
    }

    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        if($product && ($product->getCustomOption('card_type')->getValue() == 'offline'))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}