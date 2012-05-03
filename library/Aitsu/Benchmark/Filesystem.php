<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Benchmark_Filesystem implements Aitsu_Execute_Interface {

	public static function execute() {

		$strength = 10000;

		if ($_GET['p']) {
			$dir = $_GET['p'];
		} else {
			$dir = APPLICATION_PATH . '/data/cache/';
		}

		$data = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		$start = microtime(true);

		/*
		 * Add data to file system.
		 */
		for ($i = 0; $i < $strength; $i++) {
			file_put_contents($dir . '/benchmark.' . str_pad($i, 6, '0', STR_PAD_LEFT), $data);
		}

		/*
		 * Remove them from file system.
		 */
		for ($i = 0; $i < $strength; $i++) {
			unlink($dir . '/benchmark.' . str_pad($i, 6, '0', STR_PAD_LEFT));
		}

		$end = microtime(true);

		echo 'Added ' . $strength . ' file to the file system and removed them within ' . number_format(($end - $start) * 1000, 2) . ' ms.';
	}
}