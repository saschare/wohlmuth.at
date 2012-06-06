<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Html_Helper_Navigation_Ul {

	protected $_currentLevel = 0;
	protected $_startLevel = 0;
	protected $_levels = 1;
	protected $_classes = array ();
	protected $_allOpen = false;

	public static function get($nav, $startLevel, $levels, $classes = array (), $allOpen = false) {

		$instance = new self();
		$instance->_startLevel = $startLevel;
		$instance->_levels = $levels;
		$instance->_classes = $classes;
		$instance->_allOpen = $allOpen;

		return $instance->_getUl($nav, 0);
	}

	protected function _getUl(& $nav, $level) {

		if (!$this->_allOpen && !$nav->isparent && !$nav->iscurrent) {
			return '';
		}

		if ($level >= $this->_startLevel + $this->_levels) {
			return '';
		}

		$out = '';

		if ($level < $this->_startLevel) {
			foreach ($nav->children as $child) {
				$out .= $this->_getUl($child, $level +1);
			}
			return $out;
		}

		if (!$nav->haschildren) {
			return '';
		}

		$out .= '<ul';
		if (isset ($this->_classes[$level]['ul'])) {
			$out .= ' class="' . $this->_classes[$level]['ul'] . '"';
		}
		$out .= '>';

		$first = true;
		foreach ($nav->children as $child) {
			if ($child->haschildren || $child->isaccessible && $child->isvisible) {
				$out .= '<li';

				$classes = array ();
				if ($first) {
					$first = false;
					if (isset ($this->_classes[$level]['li']['first'])) {
						$classes[] = $this->_classes[$level]['li']['first'];
					}
				}
				if (isset ($this->_classes[$level]['li']['active']) && $child->iscurrent) {
					$classes[] = $this->_classes[$level]['li']['active'];
				}
				if (isset ($this->_classes[$level]['li']['parent']) && $child->isparent) {
					$classes[] = $this->_classes[$level]['li']['parent'];
				}
				if (isset ($this->_classes[$level]['li']['inactive']) && !$child->iscurrent) {
					$classes[] = $this->_classes[$level]['li']['inactive'];
				}

				if (count($classes) > 0) {
					$out .= ' class="' . implode(' ', $classes) . '"';
				}

				$out .= '>';

				$out .= '<a href="' . substr(Aitsu_Config :: get('sys.webpath'), 0, -1) . '{ref:idcat-' . $child->idcat . '}">' . $child->name . '</a>';
				if ($child->haschildren) {
					$out .= $this->_getUl($child, $level +1);
				}
				$out .= '</li>';
			}
		}

		$out .= '</ul>';

		return $out;
	}
}