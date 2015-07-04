<?php
namespace App;

class Logger {

	private $path;

	function __construct() {
		$this->path = '../logs/';
	}

	public function write($message, $level) {
		$date = date("d-m-Y");
		$filePath = $this->path.$date.".guildInf.log";
		$resource = fopen($filePath, 'a');
		$content = $level;
		$content .= " | ";
		$content .= date("H:i:s");
		$content .= " | ";
		$content .= (string) $message.PHP_EOL;
		$res = fwrite($resource, $content);
		fclose($resource);
		return $res;
	}
}