<?php


/**
 * Text parser class for aitsu.
 * @version $Id: Parser.php 16111 2010-04-23 14:20:35Z akm $
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2009, w3concepts AG
 */

class Aitsu_Text_Parser {
	
	public static function SpreadSheet2Array($text) {
		
		$rows = array();
		
		/*
		 * First we split the text into lines. We have to be aware, that
		 * line breaks could be within a table cell (i.e. within quotes).
		 */
		$lines = preg_split('/\\n(?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/s', $text);
		
		foreach ($lines as $line) {
			/*
			 * Now we split each row into cells. Here, again, we have to exclude
			 * splitting to areas outside of quotes.
			 */
			$row = preg_split('/[,;\\t](?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/s', $line);
			
			/*
			 * If there are quotes at the beginning or the end of a cell, we have to
			 * remove them.
			 */
			foreach ($row as $key => $cell) {
				$row[$key] = trim($cell, "\n\r\" ");		
			}
			
			$rows[] = $row;
		}
		
		return $rows;
	}
}