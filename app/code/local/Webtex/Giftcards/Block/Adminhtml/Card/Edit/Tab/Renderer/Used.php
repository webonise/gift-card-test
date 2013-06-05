<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Edit_Tab_Renderer_Used extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $used = $row->getData($this->getColumn()->getIndex());
        return $used >= 0 ? $used : '+'.-$used;
    }
}