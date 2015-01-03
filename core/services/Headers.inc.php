<?php

namespace core\services;

class Headers extends Service {
	private $a_headers = array();
	private $bo_forceDownload = false;
	
	/**
	* PHP5 constructor
	*/
	public function __construct(){  	
		$this->clear();
	}
	
	/**
	 * Clears the headers
	 */
	public function clear(){
		$this->a_headers = array();
		$this->contentType('text/html');
	}
	
	/**
	 * Sets the given content type
	 * 
	 * @param String $s_contentType	The content type
	 */
	public function contentType($s_contentType){
		$this->a_headers['Content-Type'] = array('Content-Type',$s_contentType);
	}
	
	/**
	 * Sets the javascript content type
	 */
	public function setJavascript(){
		$this->contentType('application/javascript');
	}
	
	/**
	 * Sets the CSS content type
	 */
	public function setCSS(){
		$this->contentType('text/css');
	}
	
	/**
	 * Sets the XML content type
	 */
	public function setXML(){
		$this->contentType('application/xml');
	}
	
	/**
	 * Sets the last modified header
	 * 
	 * @param int $i_modified	The modified time as a timestamp
	 */
	public function modified($i_modified){
		$this->a_headers['Last-Modified'] = array('Last-Modified',gmdate('D, d M Y H:i:s', $i_modified).' GMT');
	}
	
	/**
	 * Sets the cache time, -1 for no cache
	 * 
	 * @param int $i_cache	The cache time in seconds
	 */
	public function cache($i_cache){
		if( $i_cache == -1 ){
			$this->a_headers[] = array('Expires','Thu, 01-Jan-70 00:00:01 GMT');
			$this->a_headers['Last-Modified'] = array('Last-Modified',gmdate('D, d M Y H:i:s') . ' GMT');
			$this->a_headers[] = array('Cache-Control: no-store, no-cache, must-revalidate');
			$this->a_headers[] = array('Cache-Control','post-check=0, pre-check=0', false);
			$this->a_headers[] = array('Pragma','no-cache');
		}
		else {
			$this->a_headers['Expires'] = array('Expires',gmdate('D, d M Y H:i:s',(time() + $i_cache)).' GMT');
		}
	}
	
	/**
	 * Sets the content length
	 * 
	 * @param int $i_length		The length in bytes
	 */
	public function contentLength($i_length){
		$this->a_headers['Content-Length'] = array('Content-Length',$i_length);
	}
	
	/**
	 * Force downloads a file
	 * Program wil halt 
	 * 
	 * @param String $s_file		The file location
	 * @param String $s_contentType	The content type
	 */
	public function forceDownloadFile($s_file,$s_contentType){
		$i_size = filesize($s_file);
		
		$this->bo_forceDownload = true;
		$this->contentType($s_contentType);
		$this->a_headers[] = array('Content-Disposition','attachment; filename="'.basename($s_file).'"');
		$this->contentLength($i_length);
		$this->cache(-1);
		readfile($s_file);
		exit();
	}
	
	/**
	 * Force downloads the given content
	 * Program wil halt 
	 *
	 * @param String $s_content		The content to download
	 * @param String $s_contentType	The content type
	 * @param String $s_name		The name of the download
	 */
	public function forceDownloadContent($s_content,$s_contentType,$s_name){
		$i_size = strlen($s_content);
		
		$this->bo_forceDownload = true;
		$this->contentType($s_contentType);
		$this->a_headers[] = array('Content-Disposition','attachment; filename="'.$s_name.'"');
		$this->contentLength($i_length);
		$this->cache(-1);
		echo($s_content);
		exit();
	}
	
	/**
	 * Sets a header
	 * 
	 * @param String $s_key		The header key
	 * @param String $s_content	The header value
	 */
	public function setHeader($s_key,$s_content){
		$this->a_headers[] = array($s_key,$s_content);
	}
	
	/**
	 * Sends the 304 not modified header
	 */
	public function http304(){
		$this->a_headers['http'] = array('HTTP/1.1','304 Not Modified');
	}
	
	/**
	 * Sends the 400 bad request header
	 */
	public function http400(){
		$this->a_headers['http'] = array('HTTP/1.1','400 Bad Request');
	}
	
	/**
	 * Sends the 401 unauthorized header
	 */
	public function http401(){
		$this->a_headers['http'] = array('HTTP/1.1','401 Unauthorized');
	}
	
	/**
	 * Sends the 403 forbidden header
	 */
	public function http403(){
		$this->a_headers['http'] = array('HTTP/1.1','403 Forbidden');
	}
	
	/**
	 * Sends the 404 not found header
	 */
	public function http404(){
		$this->a_headers['http'] = array('HTTP/1.1','404 Not Found');
	}
	
	/**
	 * Sends the 500 internal server header
	 */
	public function http500(){
		$this->a_headers['http'] = array('HTTP/1.1','500 Internal Server Error');
	}
	
	/**
	 * Sends the 503 service unavailable header
	 */
	public function http503(){
		$this->a_headers['http'] = array('HTTP/1.1','503 Service Unavailable');
	}
	
	/**
	 * Sends the 301 redirect header
	 * Program wil halt 
	 * 
	 * @param String $s_location	The redirect location
	 */
	public function redirect($s_location){
		$this->a_headers[] = array('location',$s_location);
		$this->printHeaders();
		exit();
	}
	
	/**
	 * Returns if a force download was excecuted
	 * 
	 * @return boolean	True is the download was excecuted
	 */
	public function isForceDownload(){
		return $this->bo_forceDownload;
	}
	
	/**
	 * Returns the headers
	 * 
	 * @return array	The headers
	 */
	public function getHeaders(){
		return $this->a_headers;
	}
	
	/**
	 * Sends the cached headers to the client
	 */
	public function printHeaders(){
		$a_headers = $this->getHeaders();
		
		foreach( $a_headers AS $a_header ){
			isset($a_header[2]) ? $status = $a_header[2] : $status = true;
			
			header($a_header[0].': '.$a_header[1],$status);
		}
	}
}