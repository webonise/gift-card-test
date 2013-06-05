<?php
class Webtex_Giftcards_Block_Balance extends Mage_Core_Block_Template
{
    public function getCurrentBalance()
    {
        return Mage::helper('giftcards')->getCustomerBalance(Mage::getSingleton('customer/session')->getCustomerId());
    }
}
