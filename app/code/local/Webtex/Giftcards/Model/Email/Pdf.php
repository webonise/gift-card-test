<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Webtex_Giftcards_Model_Email_Pdf extends Varien_Object
{

    /*current page*/
    protected $_currentPage = null;

    /*current pdf*/
    protected $_currentPdf;

    /*current pdf*/
    protected $_tmpFiles=array();



    private function _initCoordinates(){

        /*doc margin*/
        $this->setTop(5);
        $this->setLeft(5);
        $this->setBottom(5);
        $this->setRight(5);

        /*start coordinates*/
        $this->setY($this->_currentPage->getHeight()-$this->getTop());
        $this->setX($this->getRight());

        $this->setInterval(5);
        $this->setLineLimit($this->_currentPage->getWidth()-$this->getRight()-$this->getLeft());
    }

    public function getPdf($data)
    {
        $this->_currentPage = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        $style = new Zend_Pdf_Style();
        $fontH = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES);
        $style->setFont($fontH, 12);
        $color = Zend_Pdf_Color_Html::color('black');
        $style->setFillColor($color);

        $linkStyle = new Zend_Pdf_Style();
        $fontH = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $linkStyle->setFont($fontH, 10);
        $color = Zend_Pdf_Color_Html::color('blue');
        $linkStyle->setFillColor($color);
        $linkStyle->setLineColor($color);

        $this->_initCoordinates();

        $this->addImage((Mage::getDesign()->getSkinUrl('images/logo_email.gif',array('_area'=>'frontend'))),30);
        $this->addNewLine(4);

        $helloStyle = new Zend_Pdf_Style();
        $fontH = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD);
        $helloStyle->setFont($fontH, 14);
        $color = Zend_Pdf_Color_Html::color('black');
        $helloStyle->setFillColor($color);
        $this->addText('Hello, '.$data->getData('email-to')."!",$helloStyle);
        $this->addNewLine(3);

        $this->addText('You have received a '.$data->getAmount().' Gift Card from ',$style);
        $fromStyle = new Zend_Pdf_Style();
        $fontH = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $fromStyle->setFont($fontH, 10);
        $this->addText($data->getData('email-from'),$fromStyle);
        $this->addText("! ",$style);
        $store = Mage::getModel('core/store')->load($data->getStoreId());
        $this->addText('This card may be redeemed on '.$store->getFrontendName().' website. Happy shopping!',$style);
        $this->addNewLine(1,$style->getFontSize());

        $this->addImage($data->getPicture(),400,$this->getLineLimit());
        $this->addNewLine(5);

        $subStyle = new Zend_Pdf_Style();
        $fontH = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD);
        $subStyle->setFont($fontH, 13);
        $color = Zend_Pdf_Color_Html::color('gray');
        $subStyle->setFillColor($color);
        $this->addText('to: '.$data->getData('email-to'),$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addText('from: '.$data->getData('email-from'),$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addText("message: ".$data->getData('email-message'),$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addText('gift card value:'.$data->getAmount(),$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addText('gift card claim code:'.$data->getCode(),$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addNewLine(5);

        $subStyle = new Zend_Pdf_Style();
        $fontH = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD);
        $subStyle->setFont($fontH, 9);
        $color = Zend_Pdf_Color_Html::color('black');
        $subStyle->setFillColor($color);
        $this->addText('To redeem and use you gift card: ',$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addText('    1. Create an account and login into '.$store->getUrl().".",$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addText('    2. Redeem the card in My Gift Cards page of My Account section.',$subStyle);
        $this->addNewLine(1,$subStyle->getFontSize());
        $this->addText('    3. Alternatively, you can redeem the card on My Cart page before proceeding to checkout.',$subStyle);
        $this->addNewLine(2,$subStyle->getFontSize());
        $this->addText('If you have any questions please contact us at '.Mage::getStoreConfig('trans_email/ident_support/email'),$subStyle);
        $phone = $data->getData('store-phone');
        if( isset($phone) ) $this->addText(' or call us at '.$phone." Monday - Friday, 8am - 5pm PST.",$subStyle);
        $this->_currentPdf = new Zend_Pdf();
        $this->_currentPdf->pages[] = $this->_currentPage;
        $this->clearTempFiles();
        return $this->_currentPdf->render();
    }


    protected function clearTempFiles(){
        foreach ($this->_tmpFiles as $file) unlink($file);
    }

    protected function addImage($url,$height=null,$weight=null){
        $ext = array_reverse(explode(".", $url));
        $ext = $ext[0];
        $name = basename($url,".".$ext);
        $tempFileName = Mage::getBaseDir('tmp')."/$name".time().".png";

        switch ($ext){
            case 'gif':
                imagepng(imagecreatefromgif($url),$tempFileName);
                break;
            case 'jpg':
                imagepng(imagecreatefromjpeg($url),$tempFileName);
                break;
            case 'png':
                file_put_contents($tempFileName,file_get_contents($url));
                break;
            default : return 0;
        }
        $this->_tmpFiles[]=$tempFileName;
        $_image = new Varien_Image($tempFileName);
        $_image->keepAspectRatio(true);
        //$_image->keepFrame(true);
        $_image->keepTransparency(true);

        if($height && $_image->getOriginalHeight()>$height){
            $_image->resize(null,$height);
        }
        if($weight && $_image->getOriginalWidth()>$weight){
            $_image->resize($weight,null);
        }

        $_image->save($tempFileName);

        $img = Zend_Pdf_Image::imageWithPath($tempFileName);

        $this->_currentPage->drawImage($img, $this->getX(), $this->getY()-$_image->getOriginalHeight(), $this->getX()+$_image->getOriginalWidth(),$this->getY());
        $this->setY($this->getY()-$_image->getOriginalHeight() - $this->getInterval());
    }

    private function addNewLine($count=1,$fontSize=0){
        $this->setY($this->getY()-($this->getInterval()+$fontSize)*$count);
        $this->setX($this->getRight());
    }

    protected function addLink($link,Zend_Pdf_Style $style,$text=""){
        $target = Zend_Pdf_Action_URI::create($link,true);
        $lines = explode("\n",$this->getWrappedText($text,$style,$this->getLineLimit())) ;
        $lastLine = null;
        $this->_currentPage->setStyle($style);
        foreach($lines as $line){
            if(trim($line)!=""){
                if($lastLine != null){
                    $this->addNewLine($style->getFontSize());
                }
                $this->_currentPage->drawText($line, $this->getX(), $this->getY());
                $annotation = Zend_Pdf_Annotation_Link::create($this->getX(), $this->getY(), $this->getX()+$this->widthForStringUsingFontSize($line,$style->getFont(),$style->getFontSize()), $this->getY(), $target);
                $this->_currentPage->attachAnnotation($annotation);
                $this->_currentPage->drawLine($this->getX(), $this->getY()-2, $this->getX()+$this->widthForStringUsingFontSize($line,$style->getFont(),$style->getFontSize()), $this->getY()-2);
                $lastLine=$line;
            }
        }
        $this->setX($this->getX()+$this->widthForStringUsingFontSize($lastLine,$style->getFont(),$style->getFontSize()));
    }

    protected function addText($text,Zend_Pdf_Style $style){
        $lines = explode("\n",$this->getWrappedText($text,$style,$this->getLineLimit())) ;
        $lastLine = null;
        $this->_currentPage->setStyle($style);
        foreach($lines as $line){
            if(trim($line)!=""){
                if($lastLine != null){
                    $this->addNewLine(1,$style->getFontSize());
                }
                $this->_currentPage->drawText($line, $this->getX(), $this->getY());
                $lastLine=$line;
            }
        }
        $this->setX($this->getX()+$this->widthForStringUsingFontSize($lastLine,$style->getFont(),$style->getFontSize()));
    }

    protected function getWrappedText($string, Zend_Pdf_Style $style,$max_width)
    {
        $localX = $this->getX();
        $wrappedText = '' ;
        $lines = explode("\n",$string) ;
        foreach($lines as $line) {
            $iteration_max_width = $max_width - $localX;
            $words = explode(' ',$line) ;
            $word_count = count($words) ;
            $i = 0 ;
            $wrappedLine = '' ;
            while($i < $word_count)
            {
                /* if adding a new word isn't wider than $iteration_max_width,
            we add the word */
                if($this->widthForStringUsingFontSize($wrappedLine.' '.$words[$i]
                    ,$style->getFont()
                    , $style->getFontSize()) < $iteration_max_width) {
                    if(!empty($wrappedLine)) {
                        $wrappedLine .= ' ' ;
                    }
                    $wrappedLine .= $words[$i] ;
                } else {
                    $wrappedText .= $wrappedLine."\n" ;
                    $localX = $this->getLeft();
                    $wrappedLine = $words[$i] ;
                }
                $i++ ;
            }
            $wrappedText .= $wrappedLine."\n" ;
        }
        return $wrappedText ;
    }
    /**
     * found here, not sure of the author :
     * http://devzone.zend.com/article/2525-Zend_Pdf-tutorial#comments-2535
     */
    protected function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8 ) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;
    }

}
