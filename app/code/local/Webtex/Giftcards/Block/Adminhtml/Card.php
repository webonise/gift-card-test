<?php

class Webtex_Giftcards_Block_Adminhtml_Card extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'giftcards';
        $this->_controller = 'adminhtml_card';
        $this->_headerText = Mage::helper('giftcards')->__('Manage Gift Cards');
        $this->_addButtonLabel = Mage::helper('giftcards')->__('Add New Gift Card');
        parent::__construct();
    } 
}
