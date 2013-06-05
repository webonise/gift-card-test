<?php

class Webtex_Giftcards_Block_Adminhtml_Cardslist_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $href = $this->getUrl('*/*/download', array('id' => $row->getListId()));
        return '<a href="'.$href.'" target="_blank">'.$this->__('Download').'</a>';
    }
}
