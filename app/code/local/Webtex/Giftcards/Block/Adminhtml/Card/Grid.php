<?php
class Webtex_Giftcards_Block_Adminhtml_Card_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardsGrid');
        $this->setDefaultSort('card_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('giftcards/giftcards')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('card_id', array(
            'header'    => Mage::helper('giftcards')->__('Card ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'card_id',
            'type'      => 'number',
        ));

        $this->addColumn('card_code', array(
            'header'    => Mage::helper('giftcards')->__('Card Code'),
            'align'     => 'left',
            'index'     => 'card_code',
        ));

        $this->addColumn('card_amount', array(
            'header'    => Mage::helper('giftcards')->__('Initial Value'),
            'align'     => 'right',
            'renderer'  => 'giftcards/adminhtml_card_grid_renderer_initial',
        ));

        $this->addColumn('card_balance', array(
            'header'    => Mage::helper('giftcards')->__('Current Balance'),
            'align'     => 'right',
            'renderer'  => 'giftcards/adminhtml_card_grid_renderer_balance',
        ));

        $this->addColumn('order_id',
            array(
                'header'    =>  Mage::helper('customer')->__('Order'),
                'width'     => '100',
                'type'      => 'action',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('View'),
                        'url'     => array('base'=>'adminhtml/sales_order/view'),
                        'field'   => 'order_id',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'getter'    => 'getOrderId',
                'frame_callback' => array('Webtex_Giftcards_Block_Adminhtml_Card_Grid', 'getOrderLink'),
        ));

        $this->addColumn('created_time', array(
            'header'    => Mage::helper('giftcards')->__('Date Created'),
            'align'     => 'left',
            'index'     => 'created_time',
            'type'      => 'datetime',
            'width'     => '160px',
        ));

        $this->addColumn('card_type', array(
            'header'    => Mage::helper('giftcards')->__('Card Type'),
            'index'     => 'card_type',
            'type'      => 'options',
            'options'   => array(
                'print' => Mage::helper('giftcards')->__('Print'),
                'email' => Mage::helper('giftcards')->__('E-mail'),
                'offline' => Mage::helper('giftcards')->__('Offline'),
            ),
        ));

        $this->addColumn('card_status', array(
            'header'    => Mage::helper('giftcards')->__('Status'),
            'index'     => 'card_status',
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('giftcards')->__('Active'),
                '0' => Mage::helper('giftcards')->__('Inactive'),
                '2' => Mage::helper('giftcards')->__('Used'),
            ),
        ));
        
        $this->addColumn('card_actions', array(
            'header'    => Mage::helper('giftcards')->__('Action'),
            'width'     => 10,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'giftcards/adminhtml_card_grid_renderer_action',
        ));

        return parent::_prepareColumns();
    }

    static function getOrderLink($renderedValue, $row, $column, $flag) {
        if ($row->getOrderId()) {
            $order = Mage::getModel('sales/order')->loadByAttribute('entity_id', $row->getOrderId());
            return str_replace('View', '#'.$order->getIncrementId(), $renderedValue);
        } else {
            return '';
        }
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $block = $this->getMassactionBlock();

        $block->setFormFieldName('card');

        $block->addItem('delete', array(
             'label'=> Mage::helper('giftcards')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('giftcards')->__('Are you sure?')
        ));
        $block->addItem('resend', array(
             'label'=> Mage::helper('giftcards')->__('Resend'),
             'url'  => $this->getUrl('*/*/massResend'),
        ));

        return $this;
    }
}
