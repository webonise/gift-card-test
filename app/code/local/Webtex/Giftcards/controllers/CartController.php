<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class Webtex_Giftcards_CartController extends Mage_Checkout_CartController
{
    public function activateGiftCardAction()
    {
        $giftCardCode = trim((string)$this->getRequest()->getParam('giftcard_code'));
        $card = Mage::getModel('giftcards/giftcards')->load($giftCardCode, 'card_code');

        if ($card->getId() && ($card->getCardStatus() == 1)) {
            $card->activateCard();

            $this->_getSession()->addSuccess(
                $this->__('Gift Card "%s" was applied.', Mage::helper('core')->escapeHtml($giftCardCode))
            );
            Mage::getSingleton('giftcards/session')->setActive('1');
            $this->_setSessionVars($card);
        } else {
            if($card->getId() && ($card->getCardStatus() == 2)) {
                $this->_getSession()->addError(
                    $this->__('Gift Card "%s" was used.', Mage::helper('core')->escapeHtml($giftCardCode))
                );
            } else {
                $this->_getSession()->addError(
                    $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode))
                );
            }
        }

        $this->_goBack();
    }

    public function deActivateGiftCardAction()
    {
        $oSession = Mage::getSingleton('giftcards/session');
        $cardId = $this->getRequest()->getParam('id');
        $cardIds = $oSession->getGiftCardsIds();
        $sessionBalance = $oSession->getGiftCardBalance();
        $newSessionBalance = $sessionBalance - $cardIds[$cardId]['balance'];
        unset($cardIds[$cardId]);
        if(empty($cardIds))
        {
            Mage::getSingleton('giftcards/session')->clear();
        }
        $oSession->setGiftCardBalance($newSessionBalance);
        $oSession->setGiftCardsIds($cardIds);
        $this->_goBack();
    }

    private function _setSessionVars($card)
    {
        $oSession = Mage::getSingleton('giftcards/session');

        $giftCardsIds = $oSession->getGiftCardsIds();

        //append applied gift card id to gift card session
        //append applied gift card balance to gift card session
        if (!empty($giftCardsIds)) {
            $giftCardsIds = $oSession->getGiftCardsIds();
            if (!array_key_exists($card->getId(), $giftCardsIds)) {
                $giftCardsIds[$card->getId()] =  array('balance' => $card->getCardBalance(), 'code' => substr($card->getCardCode(), -4));
                $oSession->setGiftCardsIds($giftCardsIds);

                $newBalance = $oSession->getGiftCardBalance() + $card->getCardBalance();
                $oSession->setGiftCardBalance($newBalance);
            }
        } else {
            $giftCardsIds[$card->getId()] = array('balance' => $card->getCardBalance(), 'code' => substr($card->getCardCode(), -4));
            $oSession->setGiftCardsIds($giftCardsIds);

            $oSession->setGiftCardBalance($card->getCardBalance());
        }
    }

    public function agreeToUseAction()
    {

        $q = Mage::getSingleton('giftcards/session')->getActive() ? 0 : 1;
        Mage::getSingleton('giftcards/session')->setActive($q);
        $result['goto_section'] = 'payment';
        $this->_getQuote()->collectTotals()->save();
        $result['update_section'] = array(
            'name' => 'payment-method',
            'html' => $this->_getPaymentMethodsHtml()
        );
        $result['giftcard_section'] = array(
            'html' => $this->_getUpdatedCoupon()
        );


        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function ajaxActivateGiftCardAction()
    {
        $giftCardCode = trim((string)$this->getRequest()->getParam('giftcard_code'));
        $card = Mage::getModel('giftcards/giftcards')->load($giftCardCode, 'card_code');

        if ($card->getId() && ($card->getCardStatus() == 1)) {

            Mage::getSingleton('giftcards/session')->setActive('1');
            $this->_setSessionVars($card);
            $this->_getQuote()->collectTotals();

        } else {
            if($card->getId() && ($card->getCardStatus() == 2)) {
                $result['error'] = $this->__('Gift Card "%s" was used.', Mage::helper('core')->escapeHtml($giftCardCode));
            } else {
                $result['error'] = $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode));
            }


        }

        $result['goto_section'] = 'payment';
        $result['update_section'] = array(
            'name' => 'payment-method',
            'html' => $this->_getPaymentMethodsHtml()
        );
        $result['giftcard_section'] = array(
            'html' => $this->_getUpdatedCoupon()
        );


        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function ajaxDeActivateGiftCardAction()
    {
        $oSession = Mage::getSingleton('giftcards/session');
        $cardId = $this->getRequest()->getParam('id');
        $cardIds = $oSession->getGiftCardsIds();
        $sessionBalance = $oSession->getGiftCardBalance();
        $newSessionBalance = $sessionBalance - $cardIds[$cardId]['balance'];
        unset($cardIds[$cardId]);
        if(empty($cardIds))
        {
            Mage::getSingleton('giftcards/session')->clear();
        }
        $oSession->setGiftCardBalance($newSessionBalance);
        $oSession->setGiftCardsIds($cardIds);

        $result['goto_section'] = 'payment';
        $result['update_section'] = array(
            'name' => 'payment-method',
            'html' => $this->_getPaymentMethodsHtml()
        );
        $result['giftcard_section'] = array(
            'html' => $this->_getUpdatedCoupon()
        );

        $this->_getQuote()->collectTotals()->save();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load(array('checkout_onepage_paymentmethod', 'giftcard_onepage_coupon'));
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->removeOutputBlock('gc');
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getUpdatedCoupon()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load(array('checkout_onepage_paymentmethod', 'giftcard_onepage_coupon'));
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->removeOutputBlock('root');
        $output = $layout->getOutput();
        return $output;
    }

}
