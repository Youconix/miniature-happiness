<?php
class HTML_Div extends HtmlItem {
	/**
	 * Generates a new div element
	 *
	 * @param String $s_content		The content
	 */
	public function __construct($s_content) {
		$this->s_tag = "<div {between}>{value}</div>";

		$this->setContent($s_content);
	}

	/**
	 * Sets the content. Adds the value if a value is allready set
	 *
	 * @param String $s_value	The value
	 */
	public function setContent($s_content) {
		$this->setValue($s_content);

		return $this;
	}
}

class HTML_PageHeader extends HTML_Div {
	/**
	 * Generates a new header element
	 *
	 * @param String $s_content		The content
	 */
	public function __construct($s_content) {
		$this->s_tag = "<header {between}>\n{value}\n</header>\n";

		$this->setContent($s_content);
	}
}

class HTML_Footer extends HTML_Div {
	/**
	 * Generates a new footer element
	 *
	 * @param String $s_content		The content
	 */
	public function __construct($s_content) {
		$this->s_tag = "<footer {between}>\n{value}\n</footer>\n";

		$this->setContent($s_content);
	}
}

class HTML_Nav extends HTML_Div {
	/**
	 * Generates a new nav element
	 *
	 * @param String $s_content		The content
	 */
	public function __construct($s_content) {
		$this->s_tag = "<nav {between}>\n{value}\n</nav>\n";

		$this->setContent($s_content);
	}
}
?>