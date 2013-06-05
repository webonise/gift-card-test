<?php

class Webtex_Giftcards_Block_Adminhtml_Cardslist extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'giftcards';
        $this->_controller = 'adminhtml_cardslist';
        $this->_headerText = Mage::helper('giftcards')->__('Download Gift Cards Created Lists');
        parent::__construct();
        $this->_removeButton('add');
    } 
}
