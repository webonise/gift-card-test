<?php

class Webtex_Giftcards_Model_Mysql4_Order extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcards/order', 'id_fake');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
}