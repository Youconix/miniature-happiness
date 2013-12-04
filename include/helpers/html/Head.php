<?php
class HTML_StylesheetLink extends CoreHtmlItem {
	/**
	 * Generates a new stylesheet link element
	 *
	 * @param   String  $s_link     The url of the link
	 * @param   String  $s_media    The media type
	 * @param   Boolean $bo_xhtml   True for XHTML-code, otherwise false
	 * @param   Boolean $bo_html5   True for HTML 5-code, otherwise false
	 */
	public function __construct($s_link, $s_media, $bo_xhtml, $bo_html5) {
		$s_type = ' type="text/css"';
		if ($bo_html5) {
			$s_type = '';
		}

		$s_media = ' media="' . $s_media . '"';

		if ($bo_xhtml) {
			$this->s_tag = '<link rel="stylesheet" href="' . $s_link . '"' . $s_type .  $s_media . ' {between}/>';
		} else {
			$this->s_tag = '<link rel="stylesheet" href="' . $s_link . '"' . $s_type . $s_media . ' {between}>';
		}
	}
}

class HTML_Stylesheet extends CoreHtmlItem {
	/**
	 * Generates the new CSS tags element
	 *
	 * @param   String  $s_css     The CSS code
	 * @param   Boolean $bo_html5  True for HTML 5-code, otherwise false
	 */
	public function __construct($s_css, $bo_html5) {
		$s_type = ' type="text/css"';
		if ($bo_html5) {
			$s_type = '';
		}

		$this->s_tag = "<style" . $s_type . ">\n<!--\n" . $s_css . "\n-->\n</style>\n";
	}
}

class HTML_JavascriptLink extends CoreHtmlItem {
	/**
	 * Generates a new Javascript link element
	 *
	 * @param   String  $s_link     The url of the link
	 * @param   Boolean $bo_html5   True for HTML 5-code, otherwise false
	 */
	public function __construct($s_link, $bo_html5) {
		$s_type = ' type="text/javascript"';
		if ($bo_html5) {
			$s_type = '';
		}

		$this->s_tag = '<script src="' . $s_link . '"' . $s_type . ' {between}></script>'."\n";
	}
}

class HTML_Javascript extends CoreHtmlItem {
	/**
	 * Generates a new Javascript tags element
	 *
	 * @param String    $s_javascript  The Javascript code
	 * @param Boolean   $bo_html5      True for HTML 5-code, otherwise false
	 */
	public function __construct($s_javascript, $bo_html5) {
		$s_type = ' type="text/javascript"';
		if ($bo_html5) {
			$s_type = '';
		}

		$this->s_tag .= "<script" . $s_type . ">\n<!--\n" . $s_javascript . "\n//-->\n</script>\n";
	}
}

class HTML_Metatag extends CoreHtmlItem {
	/**
	 * Generates a new metatag element
	 *
	 * @param String    $s_name    The name of the metatag
	 * @param String    $s_content The content of the metatag
	 * @param String    $s_scheme  The scheme of the metatag,optional
	 * @param Boolean   $bo_xhtml   True for XHTML-code, otherwise false
	 */
	public function __construct($s_name, $s_content, $s_scheme, $bo_xhtml) {
		if (!empty($s_scheme))
		$s_scheme = ' scheme="' . $s_scheme . ' ';

		$s_pre = 'name';
		if (in_array($s_name, array('refresh', 'charset', 'expires')))
		$s_pre = 'http-equiv';

		if ($bo_xhtml) {
			$this->s_tag = '<meta' . $s_scheme . ' ' . $s_pre . '="' . $s_name . '" content="' . $s_content . '" {between}/>'."\n";
		} else {
			$this->s_tag = '<meta' . $s_scheme . ' ' . $s_pre . '="' . $s_name . '" content="' . $s_content . '" {between}>'."\n";
		}
	}
}
?>