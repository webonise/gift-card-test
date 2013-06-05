<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Load extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'giftcards';
        $this->_controller = 'adminhtml_card';

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Import Gift Cards'));
        $this->_removeButton('delete');
        $this->_removeButton('back');
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Import Gift Cards from CSV');
    }
}
