<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'wts_gc_additional_prices', array(
    'group' => 'Prices',
    'sort_order' => 2,
    'backend' => 'giftcards/product_additionalprice',
    'type' => 'text',
    'input_renderer' => 'Webtex_Giftcards_Block_Adminhtml_Catalog_Product_Form_Additionalprice',
    'label' => 'Predefined Prices',
    'note' => 'List here possible gift card prices to be selected from the dropdown on the frontend. Separate them by semicolon.',
    'input' => 'text',
    'required' =>false,
    'visible' =>true,
    'visible_on_front' => false,
    'apply_to' => Webtex_Giftcards_Model_Product_Type::TYPE_GIFTCARDS_PRODUCT
));

$this->endSetup();