<?php

class Webtex_Giftcards_Block_Adminhtml_Cardscreate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('giftcards')->__('Create Gift Cards')));

        $fieldset->addField('file_path', 'text', array(
                'name'  	=> 'file_path',
                'label' 	=> Mage::helper('giftcards')->__('Path to export Cards Code'),
                'title' 	=> Mage::helper('giftcards')->__('Path to export Cards Code'),
		'required'	=> true,
            )
        );

		$fieldset->addField('count', 'text', array(
                'name'  	=> 'count',
                'label' 	=> Mage::helper('giftcards')->__('Count of Gift Cards'),
                'title' 	=> Mage::helper('giftcards')->__('Count of Gift Cards'),
		'required'	=> true
            )
        );

		$fieldset->addField('amount', 'text', array(
                'name'  	=> 'amount',
                'label' 	=> Mage::helper('giftcards')->__('Gift Cards Amount'),
                'title' 	=> Mage::helper('giftcards')->__('Gift Cards Amount'),
		'required'	=> true
            )
        );

	$createConfig = new Varien_Object();
	$createConfig->setFilePath('/var/backups/cardslist'.date('d-m-Y-His').'.csv');
	$createConfig->setCount(1);
	$createConfig->setAmount(100);
                
	$form->setValues($createConfig->getData());
        $form->setAction($this->getUrl('*/adminhtml_cardscreate/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}