<?php
$installer = $this;
$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('giftcards_cardlist')} (
    `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `file_path` text NOT NULL,
    `created_time` datetime DEFAULT NULL,
    PRIMARY KEY  (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$this->endSetup();
