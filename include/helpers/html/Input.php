<?php
class HTML_Input extends HtmlFormItem {
	protected $s_name;
	protected $s_type;

	/**
	 * Generates a new input element
	 *
	 * @param   String  $s_name     The name of the field
	 * @param   String  $s_type     The type of the field (text|password|hidden|email)
	 * @param   String  $s_value    The default text of the field
	 * @param   Boolean $bo_xhtml   True for XHTML-code, otherwise false
	 */
	public function __construct($s_name, $s_type, $s_value, $bo_xhtml) {
		$this->s_name = $s_name;
		$this->s_type = 'text';
		$this->setValue($s_value);

		if ($s_type == 'text' || $s_type == 'hidden' || $s_type == 'password' || $this->s_type == 'email')
		$this->s_type = $s_type;

		if ($bo_xhtml) {
			$this->s_tag = '<input type="{type}" name="{name}" {between} value="{value}"/>';
		} else {
			$this->s_tag = '<input type="{type}" name="{name}" {between} value="{value}">';
		}
	}

	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlFormItem::generateItem()
	 * @return  String  The (X)HTML code
	 */
	public function generateItem() {
		$this->s_tag = str_replace(array('{type}', '{name}'), array($this->s_type, $this->s_name), $this->s_tag);

		return parent::generateItem();
	}
}

class HTML_Button extends HTML_Input {
	/**
	 * Generates a new button element
	 *
	 * @param String    $s_value    The text on the button
	 * @param String    $s_name     The name of the field, leave empty for no name
	 * @param String    $s_type     The type of the field (button|reset|submit)
	 * @param Boolean   $bo_xhtml   True for XHTML-code, otherwise false
	 */
	public function __construct($s_value, $s_name, $s_type, $bo_xhtml) {
		!empty($s_name) ? $this->s_name = 'name="' . $s_name . '"' : $this->s_name = '';
		$this->s_type = 'button';
		$this->setValue($s_value);
		if ($s_type == 'button' || $s_type == 'reset' || $s_type = 'submit')
		$this->s_type = $s_type;


		if ($bo_xhtml) {
			$this->s_tag = '<input type="{type}" {name} {between} value="{value}"/>';
		} else {
			$this->s_tag = '<input type="{type}" {name} {between} value="{value}">';
		}
	}
}

class HTML_Radio extends HtmlFormItem {
	protected $bo_checked = false;

	/**
	 * Generates a new radio button element
	 *
	 * @param String	$s_name	The name
	 * @param String	$s_value The value
	 * @param Boolean $bo_xhtml True for XHTML-code, otherwise false
	 */
	public function __construct($s_name,$s_value,$bo_xhtml) {
		$this->setValue($s_value);
		 
		if ($bo_xhtml) {
			$this->s_tag = '<input type="radio" name="'.$s_name.'"{value}{checked} {between}/>';
		} else {
			$this->s_tag = '<input type="radio" name="'.$s_name.'"{value}{checked} {between}>';
		}
	}

	/**
	 * Disabled
	 */
	public function setValue($s_value) {
		$this->s_value = $s_value;
	}

	/**
	 * Sets the name
	 *
	 * @param       String  $s_name      The value of the radio button
	 */
	public function setName($s_name) {
		parent::setValue($s_name);

		return $this;
	}

	/**
	 * Sets the radio button on checked
	 */
	public function setChecked() {
		$this->bo_checked = true;

		return $this;
	}

	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlFormItem::generateItem()
	 * @return  String  The (X)HTML code
	 */
	public function generateItem() {
		$this->bo_checked ? $s_checked = ' checked="checked"' : $s_checked = '';

		if( !empty($this->s_value) ) $this->s_value = ' value="'.$this->s_value.'"';

		$this->s_tag = str_replace('{checked}', $s_checked, $this->s_tag);

		return parent::generateItem();
	}
}

class HTML_Checkbox extends HTML_Radio {
	/**
	 * Generates a new checkbox element
	 *
	 * @param String	$s_name	The name
	 * @param String	$s_value The value
	 * @param Boolean $bo_xhtml True for XHTML-code, otherwise false
	 */
	public function __construct($s_name,$s_value,$bo_xhtml) {
		$this->setValue($s_value);

		if ($bo_xhtml) {
			$this->s_tag = '<input type="checkbox" name="'.$s_name.'"{value}{checked} {between}/>';
		} else {
			$this->s_tag = '<input type="checkbox" name="'.$s_name.'"{value}{checked} {between}>';
		}
	}

	/**
	 * Sets the name
	 *
	 * @param   String  $s_name      The name of the checkbox
	 */
	public function setName($s_name) {
		parent::setValue($s_name);

		return $this;
	}
}
?>