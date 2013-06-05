<?php

$this->startSetup();

$this->run("

ALTER TABLE {$this->getTable('giftcards_card')} RENAME TO {$this->getTable('giftcards_card_bak')};

CREATE TABLE IF NOT EXISTS {$this->getTable('giftcards_card')} (
    `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `card_code` varchar(64) NOT NULL,
    `card_amount` decimal(12,4) NOT NULL,
    `card_balance` decimal(12,4) NOT NULL,
    `card_status` tinyint unsigned NOT NULL DEFAULT 0,
    `card_type` enum('print','email','offline') NOT NULL,
    `mail_from` varchar(255) NOT NULL DEFAULT '',
    `mail_to` varchar(255) NOT NULL DEFAULT '',
    `mail_to_email` varchar(255) NOT NULL DEFAULT '',
    `mail_message` text NOT NULL,
    `offline_country` varchar(255) NOT NULL DEFAULT '',
    `offline_state` varchar(255) NOT NULL DEFAULT '',
    `offline_city` varchar(255) NOT NULL DEFAULT '',
    `offline_street` varchar(255) NOT NULL DEFAULT '',
    `offline_zip` varchar(255) NOT NULL DEFAULT '',
    `offline_phone` varchar(255) NOT NULL DEFAULT '',
    `customer_id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL,
    `created_time` datetime DEFAULT NULL,
    `updated_time` datetime DEFAULT NULL,
    PRIMARY KEY  (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('giftcards_card')} (
    `card_id`,
    `card_code`,
    `card_amount`,
    `card_balance`,
    `card_status`,
    `card_type`,
    `mail_from`,
    `mail_to`,
    `mail_to_email`,
    `mail_message`,
    `customer_id`,
    `order_id`,
    `created_time`
  )
  SELECT
    `card_id` as `card_id`,
    `card_code` as `card_code`,
    `initial_value` as `card_amount`,
    `current_balance` as `card_balance`,
    IF(`status`='A',1,0) as `card_status`,
    IF(`gift_card_type`='P','print','email') as `card_type`,
    `mail_sender` as `mail_from`,
    `mail_recipient` as `mail_to`,
    `mail_address` as `mail_to_email`,
    `mail_massege` as `mail_message`,
    `customer_id` as `customer_id`,
    `order_id` as `order_id`,
    `date_created` as `created_time`
  FROM {$this->getTable('giftcards_card_bak')};

");

$attributes = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'minimal_price',
    'cost',
    'tier_price',
    'tax_class_id'
);

foreach ($attributes as $attribute) {
    $productTypes = explode(',', $this->getAttribute('catalog_product', $attribute, 'apply_to'));
    if (!in_array('giftcards', $productTypes)) {
        $productTypes[] = 'giftcards';
        $this->updateAttribute('catalog_product', $attribute, 'apply_to', join(',', $productTypes));
    }
}

$this->endSetup();
