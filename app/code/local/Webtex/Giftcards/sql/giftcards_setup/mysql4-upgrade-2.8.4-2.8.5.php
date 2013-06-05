<?php
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('giftcards/giftcards')} ADD `mail_delivery_date` DATE NULL;

");

$this->endSetup();