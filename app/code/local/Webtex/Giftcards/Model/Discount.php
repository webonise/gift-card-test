<?php

class Webtex_Giftcards_Model_Discount extends Mage_SalesRule_Model_Quote_Discount
{
    /**
     * Collect gift card discount amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Discount
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        /* Apply standard sales rules */
        parent::collect($address);

        $address->getQuote()->setUseGiftcards(false);
        $address->getQuote()->setGiftcardsDiscount(0);
        if ($this->getGiftcardDiscountEnabled()) {


            /* Append giftcard description */
            /*$descriptions = $address->getDiscountDescriptionArray();
            $descriptions[] = 'Gift Card';
            $address->setDiscountDescriptionArray($descriptions);
            $this->_calculator->prepareDescription($address);*/

            /* Save gift discount params */
            //$quote = $address->getQuote();
            //$address->getQuote()->setUseGiftcards(true);
            //$address->getQuote()->setGiftcardsDiscount($baseGiftcardsDiscount);
        }

        return $this;
    }

    protected function _collectSubtotals(Mage_Sales_Model_Quote_Address $address)
    {
        $totalItems = 0;
        $totalItemsPrice = 0;
        $totalBaseItemsPrice = 0;
        $items = $this->_getAddressItems($address);
        foreach ($items as $item) {
            //Skipping child items to avoid double calculations
            if ($item->getParentItemId()) {
                continue;
            }
            $qty = $item->getTotalQty();
            $price = ($item->getDiscountCalculationPrice() !== null) ? $item->getDiscountCalculationPrice() : $item->getCalculationPrice();
            $basePrice = ($item->getDiscountCalculationPrice() !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
            $totalItemsPrice += $price * $qty;
            $totalBaseItemsPrice += $basePrice * $qty;
            $totalItems++;
        }
        $this->_subtotals = array(
            'items_count' => $totalItems,
            'items_price' => $totalItemsPrice,
            'base_items_price' => $totalBaseItemsPrice,
        );
    }

    protected function _getSubtotal()
    {
        return $this->_subtotals['items_price'];
    }

    protected function _getBaseSubtotal()
    {
        return $this->_subtotals['base_items_price'];
    }

    protected function getGiftcardDiscountEnabled()
    {
        return (Mage::getSingleton('giftcards/session')->getActive() == '1');
    }

    protected function getAvailableGiftCardBalance()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $customerId = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
        }else{
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        }

        return Mage::helper('giftcards')->getCustomerBalance($customerId);
    }

}
