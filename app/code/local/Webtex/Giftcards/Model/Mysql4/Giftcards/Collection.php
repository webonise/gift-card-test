<?php

class Webtex_Giftcards_Model_Mysql4_Giftcards_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcards/giftcards');
        parent::_construct();
    }
}