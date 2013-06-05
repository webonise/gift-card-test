<?php

class Webtex_Giftcards_Adminhtml_GiftcardsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcards');
        $this->_addBreadcrumb($this->__('Gift Cards'), $this->__('Gift Cards'));
        $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_card'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('giftcards/giftcards')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('giftcards_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('customer/giftcards');
            $this->_addBreadcrumb($this->__('Gift Cards'), $this->__('Gift Cards'));
            $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_card_edit'))
                ->_addLeft($this->getLayout()->createBlock('giftcards/adminhtml_card_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcards')->__('Card does not exists'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function importAction()
    {
        $this->_title($this->__('Import Gift Cards from CSV'));

        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcards');
        $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_card_load'));
             //->_addLeft($this->getLayout()->createBlock('giftcards/adminhtml_card_load_tabs'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('giftcards/giftcards');
            $model->setData($data);
            $model->setId($this->getRequest()->getParam('id'));
            // set card ready for activate
            // $model->setCardStatus(2);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Gift card was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Unable find gift card to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if (($id = $this->getRequest()->getParam('id')) > 0) {
            try {
                Mage::getModel('giftcards/giftcards')->load($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Gift card was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $cardIds = $this->getRequest()->getParam('card');
        if (!is_array($cardIds)) {
            $this->_getSession()->addError($this->__('Please select gift card(s)'));
        } else {
            try {
                foreach ($cardIds as $cardId) {
                    Mage::getModel('giftcards/giftcards')->load($cardId)->delete();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d geftcard(s) were successfully deleted', count($cardIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function resendAction()
    {
        try {
            if (($cardId = $this->getRequest()->getParam('id')) > 0) {
                $card = Mage::getModel('giftcards/giftcards')->load($cardId);
                if($card->getCardType() == 'email'){
                    $card->send();
                } else {
                    $this->_getSession()->addError($this->__('Unable to send this Gift Card type.'));
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function massResendAction()
    {
        $cardIds = $this->getRequest()->getParam('card');
        if (!is_array($cardIds)) {
            $this->_getSession()->addError($this->__('Please select gift card(s)'));
        } else {
            try {
                foreach ($cardIds as $cardId) {
                    Mage::getModel('giftcards/giftcards')->load($cardId)->send();
                }
                $this->_getSession()->addSuccess(
                    $this->__('%d giftcard(s) were successfully resent', count($cardIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function printAction()
    {
        if (($cardId = $this->getRequest()->getParam('id')) > 0) {
            echo $this->getLayout()->createBlock('giftcards/adminhtml_print')->toHtml();
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function applyAction()
    {
        $customerId = trim((string) $this->getRequest()->getParam('customer_id'));
        $giftcardCode = trim((string) $this->getRequest()->getParam('giftcard_code'));
        $card = Mage::getModel('giftcards/giftcards')->load($giftcardCode, 'card_code');

        if ($card->getId() && $card->getCardStatus() == 2) {
            $card->activateCardForCustomer($customerId);
            Mage::getSingleton('customer/session')->addSuccess(
                $this->__('Gift Card "%s" was applied.', Mage::helper('core')->escapeHtml($giftcardCode))
            );
        } else {
            Mage::getSingleton('customer/session')->addError(
                $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftcardCode))
            );
        }
        echo Mage::helper('core')->currency(Mage::helper('giftcards')->getCustomerBalance($customerId));
        exit();
    }

    public function giftcardActiveAction()
    {
        if ((string)$this->getRequest()->getParam('giftcard_use') == '1') {
            Mage::getSingleton('giftcards/session')->setActive('1');
        } else {
            Mage::getSingleton('giftcards/session')->setActive('0');
        }
        try {
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->collectTotals()->save();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        exit();
    }

}