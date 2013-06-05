<?php

class Webtex_Giftcards_Block_Adminhtml_Cardscreate extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'giftcards';
        $this->_controller = 'adminhtml_cardscreate';

        $this->_updateButton('save', 'label', Mage::helper('giftcards')->__('Create Gift Cards'));
    }

    public function getHeaderText()
    {
        return Mage::helper('giftcards')->__('Create Gift Cards');
    }
}
