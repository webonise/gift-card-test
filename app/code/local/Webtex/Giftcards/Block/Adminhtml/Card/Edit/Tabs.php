<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcards_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftcards')->__('Manage Gift Cards'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('giftcards')->__('Gift Card Information'),
            'title' => Mage::helper('giftcards')->__('Gift Card Information'),
            'content' => $this->getLayout()->createBlock('giftcards/adminhtml_card_edit_tab_form')->toHtml(),
        ));

        $this->addTab('test', array(
            'label' => Mage::helper('giftcards')->__('Gift Card Statistic'),
            'title' => Mage::helper('giftcards')->__('Gift Card Statistic'),
            'content' => $this->getLayout()->createBlock('giftcards/adminhtml_card_edit_tab_statistic')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}