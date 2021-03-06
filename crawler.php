<?php

class crawler
{

	protected $_url;
	protected $_depth;
	protected $_host;
	protected $_seen 		= array();
	protected $_filter 		= array();
	protected $_visited		= array();
	protected $_images 		= array();
	protected $_internal 	= array();
	protected $_words 		= array();
	protected $_titles 		= array();
	protected $_result		= array();

	public function __construct($url, $depth=5)
	{
		$this->_url = rtrim($url,'/');
		$this->_depth = $depth;
		$parse = parse_url($url);
		$this->_host = $parse['host'];
	}


	protected function loadPage($url)
	{
		list($content, $httpCode, $time) = $this->getContent($url);
		$doc = new DOMDocument();
		$loaded = @$doc->loadHTML($content);

		if ($loaded !== false) {
			$this->_depth-=1;
			$this->crawlLinks($doc,$url);
			$this->crawlImages($doc,$url);
			$this->crawlText($doc,$url);
			$this->crawlTitle($doc,$url);
			
			$this->_seen[] = array(
				'url'=>$url, 'code'=>$httpCode, 
				'load'=>$time, 
				'internal'=>count($this->_internal[$url]), 
				'images'=>count($this->_images[$url]), 
				'words'=>$this->_words[$url], 
				'title_length'=>array_sum($this->_titles[$url])/count($this->_titles[$url]));
			
		}
	}



	protected function crawlLinks($doc,$url)
	{
		foreach ($doc->getElementsByTagName("a") as $aTag) {
			$href = rtrim($aTag->getAttribute('href'),'/'); 
			if (strpos($href,'#') === false) { 
				if (filter_var($href, FILTER_VALIDATE_URL) === false ) {
					$href = $this->_url.$href;
				}
				if($this->_url == $href) continue; 
				if(in_array($href, $this->_visited)) continue; 

				if (strpos($href, $this->_host) !== false) {	        	
					$this->_internal[$url][] = $href;
					$this->_visited[] = $href;
					if($this->_depth>0)$this->loadPage($href);
				} 
				
			}
		}
		
	}

	protected function crawlImages($doc,$url)
	{
		foreach ($doc->getElementsByTagName("img") as $imgTag) {
			$src = $imgTag->getAttribute('src');
			if(in_array($src, $this->_visited)) continue;
			$this->_images[$url][] = $src;	
			$this->_visited[] = $src;        
		}
	}

	protected function crawlText($doc,$url)
	{
		$xpath = new DOMXPath($doc);
		$nodes = $xpath->query('//text()');

		$textNodeContent = '';
		foreach($nodes as $node) {
			$textNodeContent .= " $node->nodeValue";
		}
		$this->_words[$url] = str_word_count($textNodeContent);
	}

	protected function crawlTitle($doc,$url)
	{
		foreach ($doc->getElementsByTagName("h1") as $hTag) {	  
			if(in_array($hTag->nodeValue, $this->_visited)) continue;
			$this->_titles[$url][] =  strlen($hTag->nodeValue);	        
			$this->_visited[] = $hTag->nodeValue;      
		}
		foreach ($doc->getElementsByTagName("h2") as $h2Tag) {	       
			if(in_array($h2Tag->nodeValue, $this->_visited)) continue;
			$this->_titles[$url][] =  strlen($h2Tag->nodeValue);	        
			$this->_visited[] = $h2Tag->nodeValue;              
		}
	}

	//From Project
	protected function getContent($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CrawlBot/1.0.0)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_MAXREDIRS, 15);

		$html = curl_exec($ch);
		$time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return array($html, $httpCode, $time);
	}


	public function run()
	{
		$this->loadPage($this->_url);
	}


	public function getResult()
	{
		return $this->_seen;
	}

}