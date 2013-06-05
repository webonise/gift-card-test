<?php

class Webtex_Giftcards_Model_Mysql4_Cardslist extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcards/cardslist', 'list_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
}