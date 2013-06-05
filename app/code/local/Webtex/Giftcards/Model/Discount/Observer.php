<?php
/**
 * Created by JetBrains PhpStorm.
 * User: saa
 * Date: 1/10/13
 * Time: 4:05 PM
 * To change this template use File | Settings | File Templates.
 */

class Webtex_Giftcards_Model_Discount_Observer extends Mage_Core_Model_Abstract
{
    private $_oQuote;
    private $_oAddress;
    private $_giftCardBalance;
    private $_baseGiftCardBalance;
    private $_origGiftCardBalance = 0;
    private $_baseOrigGiftCardBalance;
    private $_shippingDiscount = 0;
    private $_baseShippingDiscount = 0;
    private $_shippingDiscountAdditional = 0;
    private $_baseShippingDiscountAdditional = 0;
    private $_baseCurrency;
    private $_currentCurrency;
    private $_currencySwitch = false;
    private $_cards;

    //todo: rename method
    public function testDiscountQuote($observer)
    {
        $session = Mage::getSingleton('giftcards/session');

        //if giftcard is active
        if ($session->getActive()) {
            $this->_oQuote = $observer->getQuote();
            $this->_oQuote->setUseGiftcards(true);

            $this->_setBalanceByCurrencies(array_keys($session->getGiftCardsIds()));

            $aAddresses = $this->_oQuote->getAllAddresses();

            foreach ($aAddresses as $oAddress) {
                if ($oAddress->getGrandTotal() > 0) {
                    $this->_oAddress = $oAddress;
                    $this->_setDiscountDescription();

                    //if after shipping discount calculation gift card balance become 0,
                    //there is no need to calculate discount for items
                    if ($this->_setShippingDiscount()) {
                        $this->_setItemsDiscount();
                    }
                    //if after sets address total gift card balance become 0,
                    //there is no need to continue calculate discount for other addresses
                    if (!$this->_setAddressTotals()) {
                        break;
                    }
                }
            }
            $this->_setQuoteDiscount();
        }
    }

    private function _setShippingDiscount()
    {

        $continueDiscountCalculation = true;

        /*Check if need to add shipping to giftcard*/
        $shippingAmount = $this->_oAddress->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $this->_oAddress->getBaseShippingAmountForDiscount();
        } else {
            $shippingAmount = $this->_oAddress->getShippingAmount() ? $this->_oAddress->getShippingAmount() : 0;
            $baseShippingAmount = $this->_oAddress->getBaseShippingAmount() ? $this->_oAddress->getBaseShippingAmount() : 0;
        }

        //post process shipping amount
        if ($shippingAmount > 0) {
            $shippingDiscount = $this->_oAddress->getShippingDiscountAmount() ? $this->_oAddress->getShippingDiscountAmount() : 0;

            $this->_shippingDiscountAdditional = $shippingDiscount;

            if (($shippingAmount - $shippingDiscount - $this->_giftCardBalance) >= 0) {
                $this->_oAddress->setShippingDiscountAmount($shippingDiscount + $this->_giftCardBalance);
                //$this->_giftCardBalance = 0;
                $continueDiscountCalculation = false; //giftcardbalance = 0, so don't need to continue calculate discount
            } else {
                $this->_oAddress->setShippingDiscountAmount($shippingAmount);
                $this->_shippingDiscount = $shippingAmount - $shippingDiscount;
            }
        }

        if ($baseShippingAmount > 0) {
            $baseShippingDiscount = $this->_oAddress->getBaseShippingDiscountAmount() ? $this->_oAddress->getBaseShippingDiscountAmount() : 0;

            $this->_baseShippingDiscountAdditional = $baseShippingDiscount;

            if (($baseShippingAmount - $baseShippingDiscount - $this->_baseGiftCardBalance) >= 0) {
                $this->_oAddress->setBaseShippingDiscountAmount($baseShippingDiscount + $this->_baseGiftCardBalance);
            } else {
                $this->_oAddress->setBaseShippingDiscountAmount($baseShippingAmount);
                $this->_baseShippingDiscount = $baseShippingAmount - $baseShippingDiscount;
            }
        }
        return $continueDiscountCalculation;
    }

    private function _setDiscountDescription()
    {
        if ($this->_oAddress->getDiscountDescription()) {
            $discDescr = $this->_oAddress->getDiscountDescription() . ', Gift Card';
            $this->_oAddress->setDiscountDescription($discDescr);
        } else {
            $this->_oAddress->setDiscountDescription('Gift Card');
        }
    }

    private function _setAddressTotals()
    {
        $this->_oAddress->addTotalAmount('discount', -$this->_getAddressDiscount());
        $this->_oAddress->addBaseTotalAmount('discount', -$this->_getBaseAddressDiscount());

        //addTotalAmount sets values commented below
        //$this->_oAddress->setDiscountAmount($this->_getAddressDiscount());
        //$this->_oAddress->setBaseDiscountAmount($this->_getBaseAddressDiscount());

        $addressGrandTotal = $this->_oAddress->getGrandTotal();
        $newAddressGrandTotal = $this->_oAddress->getGrandTotal() - $this->_getAddressDiscount();

        $addressBaseGrandTotal = $this->_oAddress->getBaseGrandTotal();
        $newBaseAddressGrandTotal = $this->_oAddress->getBaseGrandTotal() - $this->_getBaseAddressDiscount();

        $this->_oAddress->setGrandTotal($newAddressGrandTotal);
        $this->_oAddress->setBaseGrandTotal($newBaseAddressGrandTotal);

        if ($addressGrandTotal - $this->_giftCardBalance >= 0) {
            //all gift card balance was used
            $this->_giftCardBalance = 0;
//            return false;
        } else {
            $this->_giftCardBalance -= $addressGrandTotal;
//            return true;
        }

        //todo: fix return
        if ($newBaseAddressGrandTotal - $this->_baseGiftCardBalance >= 0) {
            $this->_baseGiftCardBalance = 0;
            return false;
        } else {
            $this->_giftCardBalance -= $addressBaseGrandTotal;
            return true;
        }
    }

    private function _setItemsDiscount()
    {
        $items = $this->_oAddress->getAllVisibleItems();
        $availableDiscount = $this->_giftCardBalance - $this->_shippingDiscount; // + $this->_shippingDiscountAdditional;
        $baseAvailableDiscount = $this->_baseGiftCardBalance - $this->_baseShippingDiscount; // + $this->_baseShippingDiscountAdditional;

        foreach ($items as $item) {
            $qty = $item->getQty();
            $itemCalcPrice = $item->getDiscountCalculationPrice() ? $item->getDiscountCalculationPrice() - $item->getDiscountAmount() : $item->getPrice()*$qty;
            $itemDiscountAmount = $item->getDiscountAmount() ? abs($item->getDiscountAmount()) : 0;

            $baseItemCalcPrice = $item->getBaseDiscountCalculationPrice() ? $item->getBaseDiscountCalculationPrice() - $item->getBaseDiscountAmount() : $item->getBasePrice()*$qty;
            $baseItemDiscountAmount = $item->getBaseDiscountAmount() ? abs($item->getBaseDiscountAmount()) : 0;

            if ($itemCalcPrice - $itemDiscountAmount < $availableDiscount) {
                $discount = $itemCalcPrice - $itemDiscountAmount;
                $item->setDiscountAmount($discount);
                $item->setOriginalDiscountAmount($discount);
                $item->setRowTotalWithDiscount($item->getRowTotal()-$discount);
                $availableDiscount -= $discount;
            } else {
                $discount = $availableDiscount + $itemDiscountAmount;
                $item->setDiscountAmount($discount);
                $item->setOriginalDiscountAmount($discount);
                $item->setRowTotalWithDiscount($item->getRowTotal()-$discount);
                // break; // availible discount value 0, we don't need other iterations
            }

            if ($baseItemCalcPrice - $baseItemDiscountAmount < $baseAvailableDiscount) {
                $baseDiscount = $baseItemCalcPrice - $baseItemDiscountAmount;
                $item->setBaseDiscountAmount($baseDiscount);
                $item->setBaseOriginalDiscountAmount($baseDiscount);
                $baseAvailableDiscount -= $baseDiscount;
            } else {
                $baseDiscount = $baseAvailableDiscount + $baseItemDiscountAmount;
                $item->setBaseDiscountAmount($baseDiscount);
                $item->setBaseOriginalDiscountAmount($baseDiscount);
                break; //todo: fix break point
            }
        }
        $this->_oAddress->setDiscountAmount(-($discount + $this->_shippingDiscount));
        $this->_oAddress->setBaseDiscountAmount(-($baseDiscount + $this->_baseShippingDiscount));
    }

    private function _setQuoteDiscount()
    {
        $session = Mage::getSingleton('giftcards/session');

        $giftCardDiscount = $this->_getGiftCardDiscount();

        $this->_oQuote->setGiftCardsIds(array_keys($session->getGiftCardsIds()));

        //get gift card discount in base currency
        $this->_oQuote->setGiftcardsDiscount($this->_getBaseGiftCardDiscount());
        $this->_oQuote->setGrandTotal($this->_oQuote->getGrandTotal() - $this->_getGiftCardDiscount());
        $this->_oQuote->setBaseGrandTotal($this->_oQuote->getBaseGrandTotal() - $this->_getBaseGiftCardDiscount());
        $this->_oQuote->setSubtotalWithDiscount($this->_oQuote->getGrandTotal());
        $this->_oQuote->setBaseSubtotalWithDiscount($this->_oQuote->getBaseGrandTotal());

        //use for print out list of activated giftcards
        $frontData = array();

        foreach($this->_cards as $k => $v) {
            if($giftCardDiscount - $v['balance'] > 0) {
                $giftCardDiscount -= $v['balance'];
                $frontData[$k]['applied'] = $v['balance'];
                $frontData[$k]['remaining'] = 0;
            }
            else {
                $remaining = $v['balance'] - $giftCardDiscount;
                $frontData[$k]['applied'] = $giftCardDiscount;
                $frontData[$k]['remaining'] = $remaining;
                $giftCardDiscount = 0;
            }
            $frontData[$k]['code'] = $v['code'];
        }

        $session->setFrontOptions($frontData);
    }

    private function _getGiftCardDiscount()
    {
        return min($this->_oQuote->getGrandTotal(), $this->_origGiftCardBalance);
    }

    private function _getBaseGiftCardDiscount()
    {
        return min($this->_oQuote->getBaseGrandTotal(), $this->_baseOrigGiftCardBalance);
    }

    private function _getAddressDiscount()
    {
        return min($this->_oAddress->getGrandTotal(), $this->_giftCardBalance);
    }

    private function _getBaseAddressDiscount()
    {
        return min($this->_oAddress->getBaseGrandTotal(), $this->_baseGiftCardBalance);
    }

    private function _setBalanceByCurrencies($giftCardsIds)
    {

        $baseCurrency = $this->_oQuote->getBaseCurrencyCode();
        $currentCurrency = $this->_oQuote->getQuoteCurrencyCode();

        $balance = 0;
        $baseBalance = 0;
        $balanceForFront = 0;
        $cards = Mage::getModel('giftcards/giftcards')->getCollection()
            ->addFieldToFilter('card_id', array('in' => $giftCardsIds));

        foreach($cards as $card) {
            $cardCurrency = $card->getCardCurrency();
            if(is_null($cardCurrency))
            {
                $cardCurrency = $baseCurrency;
            }
            //got 1 website. or different websites but baseCurrency is same.
            if($baseCurrency == $currentCurrency) {
                if($cardCurrency != $currentCurrency) {
                    $baseBalance += Mage::helper('giftcards')->currencyConvert($card->getCardBalance(), /*from*/ $cardCurrency, /*to*/$baseCurrency);
                    $balance = $baseBalance;
                } else {
                    //if all currencies are same (only 1 store view)
                    $baseBalance += $card->getCardBalance();
                    $balance = $baseBalance;
                }
                //different websites with different baseCurrency
            } else {
                if($baseCurrency == $cardCurrency) {
                    $baseBalance +=  $card->getCardBalance();
                    $balance = Mage::helper('giftcards')->currencyConvert(/*price*/ $baseBalance,/*from*/ $baseCurrency, /*to*/$currentCurrency);
                } elseif($currentCurrency == $cardCurrency) {
                    $baseBalance += Mage::helper('giftcards')->currencyConvert($card->getCardBalance(), $currentCurrency, $baseCurrency);
                    $balance += $card->getCardBalance();
                } else {
                    $baseBalance += Mage::helper('giftcards')->currencyConvert($card->getCardBalance(), /*from*/ $cardCurrency, /*to*/$baseCurrency);
                    $balance = Mage::helper('giftcards')->currencyConvert($baseBalance, /*from*/ $baseCurrency, /*to*/$currentCurrency); //from base to current?
                }
            }

            $this->_cards[$card->getId()] = array('balance' => $balance - $balanceForFront, 'code' => substr($card->getCardCode(), -4));
            $balanceForFront = $balance;
        }

        $this->_giftCardBalance = $balance;
        $this->_baseGiftCardBalance = $baseBalance;
        $this->_origGiftCardBalance = $balance;
        $this->_baseOrigGiftCardBalance = $baseBalance;
    }
}