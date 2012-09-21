<?php


/**
 * RSS-Feed for aitsu.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Rss.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Feed_Rss {

	protected $url;
	protected $items = array ();
	protected $template;
	protected $before;
	protected $after;
	protected $templatePath;

	protected function __construct($url) {

		$this->url = $url;
		$this->templatePath = Aitsu_Registry :: get()->config->templatePath . '/';
	}

	public static function getInstance($url = null) {

		static $instance = array ();
		static $last;

		if ($url != null) {
			$last = $url;
		}

		if (!isset ($instance[$last])) {
			$instance[$last] = new self($last);
		}

		return $instance[$last];
	}

	public function fetch($seconds = null) {

		if ($this->url == null) {
			return $this;
		}

		if ($seconds != null) {
			$p = Aitsu_Persistence :: getInstance('rssFeed', $this->url);
			if ($p->items == null) {
				$channel = new Zend_Feed_Rss($this->url);
				foreach ($channel as $item) {
					$this->items[] = array (
						'title' => utf8_decode($item->title()),
						'link' => utf8_decode($item->link()),
						'description' => utf8_decode($item->description())
					);
				}
				$p->items = $this->items;
				$p->save($seconds);
			} else {
				$this->items = $p->items;
			}
		} else {
			$channel = new Zend_Feed_Rss($this->url);
			foreach ($channel as $item) {
				$this->items[] = $item;
			}
		}

		return $this;
	}

	public function setTemplate($template) {

		if ($template == null) {
			return $this;
		}

		$this->template = $this->readTemplateContent($template);

		return $this;
	}

	protected function readTemplateContent($template) {

		if ($template == null || $template == '') {
			return '';
		}

		$filename = $this->templatePath . $template;
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);

		if (preg_match('@<articleList(.*?)\\s*/?>@', $contents, $match)) {
			$returnValue = str_replace($match[0], '', $contents);
		} else {
			return $contents;
		}

		preg_match_all('/(\\w*)=\"(.*?)\"/', $match[1], $matches);
		for ($i = 0; $i < count($matches[0]); $i++) {
			$this->configuration[$matches[1][$i]] = $matches[2][$i];
		}

		if (preg_match('/<!-{2}\\s*before:start\\s*-{2}>(.*?)<!-{2}\\s*before:end\\s*-{2}>/s', $returnValue, $match)) {
			$this->before = $match[1];
			$returnValue = str_replace($match[0], '', $returnValue);
		}

		if (preg_match('/<!-{2}\\s*after:start\\s*-{2}>(.*?)<!-{2}\\s*after:end\\s*-{2}>/s', $returnValue, $match)) {
			$this->after = $match[1];
			$returnValue = str_replace($match[0], '', $returnValue);
		}

		return $returnValue;
	}

	public function getOut($numberOfItems) {

		if (count($this->items) == 0) {
			return $this->altText;
		}

		for ($i = 0; $i < $numberOfItems && $i < count($this->items); $i++) {
			$tmp = $this->template;
			$tmp = str_replace('{title}', $this->items[$i]['title'], $tmp);
			$tmp = str_replace('{link}', $this->items[$i]['link'], $tmp);
			$tmp = str_replace('{description}', $this->items[$i]['description'], $tmp);
			$out .= $tmp;
		}

		if (preg_match_all('/\\[if\\s*(.*?)\\](.*?)\\[endif\\]/s', $out, $matches) > 0) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				if (eval ('if ' . $matches[1][$i] . ' {return true;} return false;')) {
					$out = str_replace($matches[0][$i], $matches[2][$i], $out);
				} else {
					$out = str_replace($matches[0][$i], '', $out);
				}
			}
		}

		return $this->before . $out . $this->after;
	}

}