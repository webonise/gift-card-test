<?php

class Webtex_Giftcards_Block_Print extends Mage_Core_Block_Template
{
	public function getGiftcard() {

        if (($cardCode = $this->getRequest()->getParam('code'))) {
            $card = Mage::getModel('giftcards/giftcards')->load($cardCode, 'card_code');
            if ($card->getCardStatus() == 1)
            {
                return $card;
            }
        }
		return false;
	}
}