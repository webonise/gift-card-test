<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Edit_Tab_Renderer_Balance extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $card = Mage::registry('giftcards_data');
        $balance = $card->getData('temp_amount') - $row->getData($this->getColumn()->getIndex());
        $card->setData('temp_amount', $balance);
        echo $balance;
    }
}