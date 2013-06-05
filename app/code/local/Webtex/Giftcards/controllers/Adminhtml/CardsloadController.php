<?php

class Webtex_Giftcards_Adminhtml_CardsloadController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcards');
        $this->_addBreadcrumb($this->__('Import Gift Cards'), $this->__('Import Gift Cards'));
        $this->_addContent($this->getLayout()->createBlock('giftcards/adminhtml_cardsload'));
        $this->renderLayout();
    }

    public function saveAction()
    {
	$request = $this->getRequest();

	$path		= '';
	$delimiter	= $request->getParam('delimiter', false);
	$enclosure	= $request->getParam('enclosure', false);

	try {
		$file = $_FILES['file']['name'];
		$path = Mage::getBaseDir('var').DS.'import'.DS;
		$uploader = new Varien_File_Uploader('file');
		$uploader->setAllowRenameFiles(false);
		$uploader->setFilesDispersion(false);
		$uploader->save($path, $file);

		$io = new Varien_Io_File();
		$io->open(array('path' => $path));
		$io->streamOpen($path.$file, 'r');
		$io->streamLock(true);

		$map = $io->streamReadCsv($delimiter, $enclosure);
			
		while($data = $io->streamReadCsv($delimiter, $enclosure)){
		    if($data[0]){
                                $model = Mage::getModel('giftcards/giftcards');
                                $model->setCardAmount($data[1]);
                                $model->setCardCode($data[0]);
                                $model->setCardStatus(1);
                                $model->save();
	            } else {
		        continue;
		    }
		}

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('giftcards')->__('Gift Cards where succesfully imported '));
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcards')->__($e->getMessage().'An error occurred while importing Gift Cards.'));
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/*"));
    }
}