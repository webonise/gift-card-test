<?php
/**
 * Created by JetBrains PhpStorm.
 * User: usr
 * Date: 05.10.12
 * Time: 17:35
 * To change this template use File | Settings | File Templates.
 */

class Webtex_Giftcards_Model_Product_Additionalprice extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function validate($object)
    {

        $validator = new Zend_Validate_Regex(array('pattern' => '/(^\d+(\.{0,1}\d{0,})(;\d+(\.{0,1}\d{0,}))+$)|^$/'));

        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        $price = $object->getData('price');
        if(!($price > 0))
        {
            if(!$validator->isValid($value))
            {
                Mage::throwException('Not correct value. Example: 100;200.33;300.56');
            }
        }
    }
}