<?php

class Webtex_Giftcards_Block_Product_Type extends Mage_Catalog_Block_Product_View_Abstract
{
    protected $priceStatus;
    protected $aAdditionalPrices;
    private $_cardType;

    public function _construct()
    {
        $product = $this->getProduct();

        $this->_cardType = $product->getAttributeText('wts_gc_type');

        if($this->getProduct()->getPrice() == 0)
        {
            $this->initPriceStatus();
        }
        parent::_construct();
    }

    protected function initPriceStatus()
    {

        if($this->getProduct()->getWtsGcAdditionalPrices())
        {
            $this->priceStatus = '2';
            $this->aAdditionalPrices = explode(';',$this->getProduct()->getWtsGcAdditionalPrices());
        }
        else
        {
            $this->priceStatus = '1';
        }
    }

    public function getPriceStatus()
    {
        return $this->priceStatus;
    }

    public function getCardType()
    {
        return $this->_cardType;
    }
}
