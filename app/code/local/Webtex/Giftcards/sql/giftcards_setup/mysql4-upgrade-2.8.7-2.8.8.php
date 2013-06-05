<?php
$installer = $this;
$installer->startSetup();

$installer->run("

UPDATE {$this->getTable('giftcards/giftcards')} SET `card_status` = 1 WHERE `card_status` = 2;

");

$this->endSetup();