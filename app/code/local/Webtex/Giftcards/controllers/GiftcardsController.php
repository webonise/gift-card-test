<?php

class Webtex_Giftcards_GiftcardsController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('*/*/balance');
    }

    public function printAction()
    {
        if (($cardCode = $this->getRequest()->getParam('code'))) {
            $this->loadLayout('print');
            $this->renderLayout();
        } else {
            $this->_redirect('/');
        }
    }
}
