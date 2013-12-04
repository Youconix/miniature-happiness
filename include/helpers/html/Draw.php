<?php
class HTML_Canvas extends HtmlItem {
	/**
	 * Generates a new canvas element
	 */
	public function __construct(){
		$this->s_tag	 = '<canvas {between}></canvas>';
	}	
}
?>