<?php

class Webtex_Giftcards_Block_Adminhtml_Card_Edit_Tab_Statistic extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('giftCardStatistic');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('ASC');
        //$this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $salesFlatOrder =  Mage::getSingleton('core/resource')->getTableName('sales/order');
        $card = Mage::registry('giftcards_data');
        Mage::registry('giftcards_data')->setData('temp_amount', $card->getCardAmount());
        $collection = Mage::getModel('giftcards/order')->getCollection()
            ->addFieldToFilter('id_giftcard', $card->getId());
        $collection->getSelect()->join($salesFlatOrder, $salesFlatOrder.'.entity_id=main_table.id_order', $salesFlatOrder.'.increment_id as increment_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_n', array(
            'header'    => Mage::helper('giftcards')->__('Order N'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'increment_id',
            'type'      => 'number',
        ));

        $this->addColumn('used_amount', array(
            'header'    => Mage::helper('giftcards')->__('Used Amount'),
            'align'     => 'left',
            'index'     => 'discounted',
            'renderer'  => 'giftcards/adminhtml_card_edit_tab_renderer_used'
        ));

        $this->addColumn('balance', array(
            'header'    => Mage::helper('giftcards')->__('Balance'),
            'align'     => 'left',
            'index'     => 'discounted',
            'type'      => 'number',
            'renderer'  => 'giftcards/adminhtml_card_edit_tab_renderer_balance'
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('giftcards')->__('Created At'),
            'align'     => 'left',
            'index'     => 'created_time',
            'type'      => 'datetime',
            'width'     => '160px',
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/sales_order/view/edit', array('order_id' => $row->getIdOrder()));
    }
}