<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'giftcards';
        $this->_controller = 'adminhtml_card';

        $this->_updateButton('save', 'label', Mage::helper('giftcards')->__('Save Gift Card'));
        $this->_updateButton('delete', 'label', Mage::helper('giftcards')->__('Delete Gift Card'));

        if (Mage::registry('giftcards_data')->getId()) {
            $this->_addButton('resend', array(
                'label' => Mage::helper('giftcards')->__('Resend Gift Card'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/resend', array('id' => Mage::registry('giftcards_data')->getId())) . '\')',
            ), 2);
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('giftcards_data') && Mage::registry('giftcards_data')->getId()) {
            return Mage::helper('giftcards')->__("Edit Gift Card '%s'", $this->escapeHtml(Mage::registry('giftcards_data')->getCardCode()));
        } else {
            return Mage::helper('giftcards')->__('New Gift Card');
        }
    }
}
