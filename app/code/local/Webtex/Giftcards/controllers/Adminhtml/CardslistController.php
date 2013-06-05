<?php

class Webtex_Giftcards_Adminhtml_CardslistController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcards');
        $this->_addBreadcrumb($this->__('Gift Cards List'), $this->__('Download Gift Cards List'));
        $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_cardslist'));
        $this->renderLayout();
    }
    
    public function downloadAction()
    {
        $list = Mage::getModel('giftcards/cardslist')->load($this->getRequest()->getParam('id'));

        if(!$list->getId()){
           return;
        }

        $fileName = $list->getFilePath();
        $contentLength = filesize($fileName);
        $names = pathinfo($fileName);

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-Type', 'application/download; name=' . $names['basename'], true)
            ->setHeader('Content-Length', $contentLength)
            ->setHeader('Content-Disposition', 'filename='.$names['basename']);
        $this->getResponse()->sendHeaders();
        readfile($fileName);
    }
}
