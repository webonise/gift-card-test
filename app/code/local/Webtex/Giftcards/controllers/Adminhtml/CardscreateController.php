<?php

class Webtex_Giftcards_Adminhtml_CardscreateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcards');
        $this->_addBreadcrumb($this->__('Create Gift Cards'), $this->__('Create Gift Cards'));
        $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_cardscreate'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $cards = array();
            try {
                for($i=0;  $i< $data['count']; $i++) {
                    $model = Mage::getModel('giftcards/giftcards');
                    $model->setCardAmount($data['amount']);
                    // set card ready for activate
                    $model->setCardStatus(1);
                    $model->save();
                    $cards[$i]['code'] = $model->getCardCode();
                    $cards[$i]['amount'] = $model->getCardAmount();
                }
                $this->_printList($cards,$data['file_path']);
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Gift cards was successfully created'));
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

	private function _printList($cards, $path)
	{
	    try {
		$io = new Varien_Io_File();
		$fullPath = Mage::getBaseDir() . $path;
		$parts = pathinfo($fullPath);
		if(!isset($parts['extension']) || strtolower($parts['extension']) != 'csv'){
			Mage::throwException('Error in file extension. Only *.csv files are supported');
		}

                $delimiter = ';';
                $enclosure = '"';
		$io->open(array('path' => $parts['dirname']));
                $io->streamOpen($fullPath, 'w+');
                $io->streamLock(true);

		$header = array('card_id'   => 'Gift Card Code',
		                'amount'    => 'Card Amount',
		                );
		$io->streamWriteCsv($header, $delimiter, $enclosure);

                $content = array();
		foreach($cards as $card){
		        $content['card_id'] = $card['code'];
		        $content['amount']  = $card['amount'];
			$io->streamWriteCsv($content, $delimiter, $enclosure);
		}
	        $io->streamUnlock();
                $io->streamClose();
                $list = Mage::getModel('giftcards/cardslist')->load($fullPath,'file_path');
                $list->setFilePath($fullPath)->save();
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcards')->__('An error occurred while save cards list.'));
            }
		
	}

}