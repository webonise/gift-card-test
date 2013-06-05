<?php
$installer = $this;
$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('giftcard_order')} (
    `id_fake` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_giftcard` int(10) NOT NULL,
    `id_order` int(10) NOT NULL,
    `discounted` decimal(12,4) NOT NULL,
    `created_time` datetime DEFAULT NULL,
    PRIMARY KEY  (`id_fake`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$this->endSetup();
