<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $card = Mage::registry('giftcards_data');
        $form = new Varien_Data_Form();

        $cardFieldset = $form->addFieldset('giftcards_form', array(
            'legend' => Mage::helper('giftcards')->__('Gift Card Info')
        ));

        if ($card->getCardId()) {
            $cardFieldset->addField('card_id', 'hidden', array(
                'name' => 'card_id',
            ));
        }

        $cardFieldset->addField('auto_card_code', 'checkbox', array(
            'name'    => 'auto_card_code',
            'label'   => Mage::helper('giftcards')->__('Generate Card Code'),
            'checked' => 'checked',
            'onchange'=> 'if(this.checked){$(\'card_code\').disabled = true} else {$(\'card_code\').disabled = false};',
        ));

        $cardFieldset->addField('card_code', 'text', array(
            'name'     => 'card_code',
            'label'    => Mage::helper('giftcards')->__('Enter Card Code'),
            'disabled' => 'true',
            'required' => 'true',
        ));

        if ($card->getCardId()) {
            $cardFieldset->addField('card_amount', 'label', array(
                'name' => 'card_amount',
                'label' => Mage::helper('giftcards')->__('Initial Value'),
            ));
            if (0 && $card->getOrderId()) {
                $order = Mage::getModel('sales/order')->loadByAttribute('entity_id', $card->getOrderId());
                $cardFieldset->addField('order_id', 'link', array(
                    'name' => 'order_id',
                    'label' => Mage::helper('giftcards')->__('Order'),
                    'value' => $order->getIncrementId(),
                    'href'  => $this->getUrl('adminhtml/sales_order/view', array('order_id' => $card->getOrderId())),
                ));
            }
        } else {
            $cardFieldset->addField('card_amount', 'text', array(
                'name' => 'card_amount',
                'label' => Mage::helper('giftcards')->__('Initial Value'),
                'value_filter' => $this,
            ));
        }

        $cardFieldset->addField('card_currency', 'text', array(
            'name' => 'card_currency',
            'label' => 'Card Currency',
        ));

        if ($card->getCardId()) {
            $cardFieldset->addField('card_balance', 'text', array(
                'name' => 'card_balance',
                'label' => Mage::helper('giftcards')->__('Current Balance'),
                'value_filter' => $this,
            ));
        }

        $cardFieldset->addField('card_status', 'select', array(
            'name' => 'card_status',
            'label' => Mage::helper('giftcards')->__('Status'),
            'options' => array(
                '1' => Mage::helper('giftcards')->__('Active'),
                '0' => Mage::helper('giftcards')->__('Inactive'),
                '2' => Mage::helper('giftcards')->__('Used'),
            ),
        ));

        $cardFieldset->addField('card_type', 'select', array(
            'name' => 'card_type',
            'label' => Mage::helper('giftcards')->__('Gift Card Type'),
            'options' => array(
                'email' => Mage::helper('giftcards')->__('E-mail'),
                'print' => Mage::helper('giftcards')->__('Print'),
                'offline' => Mage::helper('giftcards')->__('Offline'),
            ),
        ));

        $recipientFieldset = $form->addFieldset('recipient_form', array(
            'legend' => Mage::helper('giftcards')->__('Recipient Info')
        ));

        $recipientFieldset->addField('mail_to', 'text', array(
            'name' => 'mail_to',
            'label' => Mage::helper('giftcards')->__('To Name'),
        ));

        $recipientFieldset->addField('mail_to_email', 'text', array(
            'name' => 'mail_to_email',
            'label' => Mage::helper('giftcards')->__('To Email'),
        ));

        $recipientFieldset->addField('mail_from', 'text', array(
            'name' => 'mail_from',
            'label' => Mage::helper('giftcards')->__('From Name'),
        ));

        $recipientFieldset->addField('mail_message', 'textarea', array(
            'name' => 'mail_message',
            'label' => Mage::helper('giftcards')->__('Message'),
            'style' => 'height:70px',
        ));


        $this->setForm($form);
        $form->setValues($card->getData());

        return parent::_prepareForm();
    }

    public function filter($value)
    {
        return number_format($value, 2);
    }
}