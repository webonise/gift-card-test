<?php
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('sales/quote')} ADD `use_giftcards` tinyint(1) NULL;
ALTER TABLE {$this->getTable('sales/quote')} ADD `giftcards_discount` decimal(12,4) NULL;

");

$installer->endSetup();

