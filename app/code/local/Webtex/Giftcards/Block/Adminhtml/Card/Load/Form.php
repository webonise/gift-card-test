<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Load_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('giftcards')->__('Import Gift Card')));

        $fieldset->addField('file', 'file', array(
                'name'  	=> 'file',
                'label'		=> Mage::helper('giftcards')->__('Path to file for Gift Cards'),
                'title' 	=> Mage::helper('giftcards')->__('Path to file for Gift Cards'),
				'required'	=> true
            )
        );

	$fieldset->addField('delimiter', 'text', array(
                'name'  	=> 'delimiter',
                'label' 	=> Mage::helper('giftcards')->__('Value delimiter'),
                'title' 	=> Mage::helper('giftcards')->__('Value delimiter'),
				'required'	=> true
            )
        );

	$fieldset->addField('enclosure', 'text', array(
                'name'  	=> 'enclosure',
                'label' 	=> Mage::helper('giftcards')->__('Enclose Values In'),
			'title' => Mage::helper('giftcards')->__('Enclose Values In'),
				'required'	=> true
            )
        );


        $form->setAction($this->getUrl('*/*/saveImport'));
        $form->setMethod('post');
        $form->setId('load_form');
	$form->setEnctype('multipart/form-data');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}