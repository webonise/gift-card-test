<?php

class Webtex_Giftcards_Model_Mysql4_Giftcards extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcards/giftcards', 'card_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
}