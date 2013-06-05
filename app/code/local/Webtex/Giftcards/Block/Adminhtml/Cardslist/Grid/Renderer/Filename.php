<?php

class Webtex_Giftcards_Block_Adminhtml_Cardslist_Grid_Renderer_Filename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $parts = pathinfo($row->getFilePath());
        return $parts['basename'];
    }
}
