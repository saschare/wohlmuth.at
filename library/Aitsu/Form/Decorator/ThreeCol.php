<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Yaml.php 18472 2010-08-31 14:54:59Z akm $}
 */

class Aitsu_Form_Decorator_ThreeCol extends Zend_Form_Decorator_ViewHelper {

	public function render($content) {

		$element = $this->getElement();

        $view = $element->getView();
        if (null === $view) {
            require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('ViewHelper decorator cannot render without a registered view object');
        }

        if (method_exists($element, 'getMultiOptions')) {
            $element->getMultiOptions();
        }

        $helper        = $this->getHelper();
        $separator     = $this->getSeparator();
        $value         = $this->getValue($element);
        $attribs       = $this->getElementAttribs();
        $name          = $element->getFullyQualifiedName();
        $id            = $element->getId();
        $attribs['id'] = $id;

        $helperObject  = $view->getHelper($helper);
        if (method_exists($helperObject, 'setTranslator')) {
            $helperObject->setTranslator($element->getTranslator());
        }

		$chunks = array_chunk($element->options, ceil(count($element->options)/4));
		$attribChunks = array_chunk($attribs, ceil(count($attribs)/4));
		
		$elementContent = '<div class="subcolumns"><div class="c25l"><div class="subcl">';
		$elementContent .= $view->$helper($name, $value, $attribChunks[0], $chunks[0]) . '</div></div>';
		$elementContent .= '<div class="c25l"><div class="subcl">';
		$elementContent .= $view->$helper($name, $value, $attribChunks[1], $chunks[1]) . '</div></div>';
		$elementContent .= '<div class="c25l"><div class="subcl">';
		$elementContent .= $view->$helper($name, $value, $attribChunks[2], $chunks[2]) . '</div></div>';
		$elementContent .= '<div class="c25r"><div class="subcl">';
		$elementContent .= $view->$helper($name, $value, $attribChunks[3], $chunks[3]) . '</div></div>';
		$elementContent .= '</div>';

        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $separator . $elementContent;
            case self::PREPEND:
                return $elementContent . $separator . $content;
            default:
                return $elementContent;
        }
	}
}