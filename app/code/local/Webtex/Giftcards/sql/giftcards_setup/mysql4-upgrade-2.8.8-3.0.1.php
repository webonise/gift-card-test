<?php
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('giftcards/giftcards')} ADD `card_currency` VARCHAR(50) NULL;

");

$this->endSetup();