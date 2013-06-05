<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $href = $this->getUrl('*/*/print', array('id' => $row->getCardId()));
        return '<a href="'.$href.'" target="_blank">'.$this->__('Print').'</a>';
    }
}
