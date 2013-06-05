<?php
class Webtex_Giftcards_Block_Adminhtml_Cardslist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardsListGrid');
        $this->setDefaultSort('list_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('giftcards/cardslist')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('list_id', array(
            'header'    => Mage::helper('giftcards')->__('List ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'list_id',
            'type'      => 'number',
        ));

        $this->addColumn('file_path', array(
            'header'    => Mage::helper('giftcards')->__('Filename'),
            'align'     => 'left',
            'index'     => 'file_path',
            'renderer'  => 'giftcards/adminhtml_cardslist_grid_renderer_filename',
        ));

        $this->addColumn('created_time', array(
            'header'    => Mage::helper('giftcards')->__('Date Created'),
            'align'     => 'left',
            'index'     => 'created_time',
            'type'      => 'datetime',
            'width'     => '160px',
        ));

        $this->addColumn('card_actions', array(
            'header'    => Mage::helper('giftcards')->__('Action'),
            'width'     => 10,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'giftcards/adminhtml_cardslist_grid_renderer_action',
        ));

        return parent::_prepareColumns();
    }

}
