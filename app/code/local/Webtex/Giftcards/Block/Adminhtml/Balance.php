<?php
class Webtex_Giftcards_Block_Adminhtml_Balance extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function getCurrentBalance(){
        return Mage::helper('giftcards')->getCustomerBalance($this->getCustomerId());
    }
}
