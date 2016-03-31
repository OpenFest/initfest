<?php
class SmartCurl {
	protected static $etags = [];
	protected $ch = null;
	protected $cache_dir = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
	protected $cache_index;
	protected $url_root = null;
	
	public function __construct($url_root = null, $cache_dir = null) {
		if (!is_null($cache_dir)) {
			$this->cache_dir = __DIR__ . DIRECTORY_SEPARATOR . $cache_dir;
		}
		
		if (!file_exists($this->cache_dir)) {
			mkdir($this->cache_dir);
		}
		
		$this->cache_index = $this->cache_dir . '.json';
		
		$cache = file_exists($this->cache_index) ? file_get_contents($this->cache_index) : false;

		if ($cache !== false) {
			$cache = json_decode($cache, true);
		}
		
		if ($cache !== false) {
			static::importEtags($cache);
		}
		
		if (!is_null($url_root)) {
			$this->url_root = $url_root;
		}
		
		$ch = curl_init();
		
		if ($ch === false) {
			throw new Exception('curl init failed');
		}
		
		$this->ch = $ch;
		
		if (curl_setopt_array($ch, [
			CURLOPT_FAILONERROR => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			//CURLINFO_HEADER_OUT => true,
		]) === false) {
			throw new Exception('curl setopt failed');
		}
	}
	
	public function __destruct() {
		curl_close($this->ch);
		file_put_contents($this->cache_index, json_encode(static::exportEtags()));
	}
	
	public static function importEtags(array $etags) {
		static::$etags = array_merge(static::$etags, $etags);
	}
	
	public static function exportEtags() {
		return static::$etags;
	}
	
	public function getUrl($filename) {
		if (is_null($this->url_root)) {
			$url = $filename;
		}
		else {
			$url = $this->url_root . $filename;
		}
		
		if (curl_setopt($this->ch, CURLOPT_URL, $url) === false) {
			throw new Exception('set url failed');
		}
		
		$cache_file = $this->cache_dir . DIRECTORY_SEPARATOR . $filename;
		
		$etag = array_key_exists($url, static::$etags) && file_exists($cache_file) ? static::$etags[$url] : null;
		
		if (curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
			'If-None-Match:' . (is_null($etag) ? '' : ' ' . $etag),
		]) === false) {
			throw new Exception('set etag failed');
		}
	
		$response = curl_exec($this->ch);
		
		if ($response === false) {
			if (file_exists($cache_file)) {
				return file_get_contents($cache_file);
			}
			else {
				return false;
			}
		}
		
		//var_dump(curl_getinfo($this->ch, CURLINFO_HEADER_OUT));
		//var_dump($response);
		
		$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$headers = array_filter(explode("\r\n", $header));
		
		foreach ($headers as $header_line) {
			if (stripos($header_line, 'etag:') === 0) {
				static::$etags[$url] = trim(substr($header_line, strlen('etag:')));
			}
		}
		
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

		if ($http_code === 304 && !is_null($this->url_root)) {
			// use cache
			
			if (file_exists($cache_file)) {
				return file_get_contents($cache_file);
			}
			else {
				return false;
			}
		}
		
		if (strlen($response) === $header_size) {
			return false;
		}
		
		$body = substr($response, $header_size);
		
		if ($http_code === 200) {
			$dirname = dirname($filename);
			
			if ($dirname !== '.') {
				mkdir($this->cache_dir . DIRECTORY_SEPARATOR . $dirname, 0777, true);
			}
			
			file_put_contents($cache_file, $body);
		}
		
		return $body;
	}
}
