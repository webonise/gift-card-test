<?php
class Webtex_Giftcards_Block_Purchased extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate("webtex/giftcards/purchased.phtml");

        $cards = Mage::getModel('giftcards/giftcards')->getCollection();
        $cards->getSelect()
                ->join( array('so'=>Mage::getSingleton('core/resource')->getTableName('sales/order')), 'main_table.order_id = so.entity_id', array('main_table.*','so.increment_id as order_increment_id'))
                ->where('so.customer_id = '.Mage::helper('customer')->getCustomer()->getId());

        $this->setCards($cards);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('Purchased Gift Cards'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'webtex.giftcards.purchased.pager')
            ->setCollection($this->getCards());
        $this->setChild('pager', $pager);
        $this->getCards()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
