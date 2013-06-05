<?php

class Webtex_Giftcards_Block_Adminhtml_Print extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('webtex/giftcards/print.phtml');
    }

    public function getGiftcard() {
        if (($cardId = $this->getRequest()->getParam('id')) > 0) {
            $card = Mage::getModel('giftcards/giftcards')->load($cardId);
            return $card;
        }
        return false;
    }
}
