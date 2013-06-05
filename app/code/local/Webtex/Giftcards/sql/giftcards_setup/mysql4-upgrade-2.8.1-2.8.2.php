<?php
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('giftcards/giftcards')} ADD `product_id` int(11) NULL;

");

$installer->endSetup();

