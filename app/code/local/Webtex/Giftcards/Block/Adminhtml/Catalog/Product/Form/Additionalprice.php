<?php
/**
 * Created by JetBrains PhpStorm.
 * User: usr
 * Date: 09.10.12
 * Time: 13:23
 * To change this template use File | Settings | File Templates.
 */

class Webtex_Giftcards_Block_Adminhtml_Catalog_Product_Form_Additionalprice extends Varien_Data_Form_Element_Text
{
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        return $html . "
        <script>
            (function(){
                var oAddtitionalPrices = $('" . $this->getHtmlId() . "');
                var price = $('price');
                function checkStatus()
                {
                    if((+price.value) > 0)
                    {
                        oAddtitionalPrices.disable().setStyle({
                            background: '#E6E6E6'
            });
                    }
                    else
                    {
                        oAddtitionalPrices.enable().setStyle({
                            background: 'white'
            });
                    }
                }

                (function init()
                {
                    checkStatus();
                     price.observe('blur',function(e){
                        checkStatus();
                     });
                })();


            })();
        </script>
        ";
    }
}