<?php
class InputFactory {
	private static $_instance;
	
	public static function getInstance(){
		if( is_null(self::$_instance) ){
			self::$_instance = new InputFactory();
		}
		
		return self::$_instance;
	}
	
	protected function __clone(){}
	
	public function input($s_name,$s_type,$s_value,$s_htmlType){
		return new HTML_Input($s_name,$s_type,$s_value,$s_htmlType);
	}
	
	public function range($s_name,$i_value){
		return new HTML_Range($s_name,$i_value);
	}
	
	public function number($s_name,$i_value){
		return new HTML_Number($s_name,$i_value);
	}
	
	public function date($s_name,$i_value){
		return new HTML_Date($s_name,$i_value);
	}
	
	public function datetime($s_name,$bo_local){
		return new HTML_Datetime($s_name,$bo_local);
	}
	
	public function button($s_name,$s_type,$s_value,$s_htmlType){
		return new HTML_Button($s_value,$s_name,$s_type,$s_htmlType);
	}
	
	public function radio($s_name,$s_value,$s_htmlType){
		return new HTML_Radio($s_name,$s_value,$s_htmlType);
	}
	
	public function checkbox($s_name,$s_value,$s_htmlType){
		return new HTML_Checkbox($s_name,$s_value,$s_htmlType);
	}
}

abstract class CoreHTML_Input extends HtmlFormItem {
	protected $s_type;
	
	/**
	 * Constructor 
	 * 
	 * @param   string  $s_name     The name of the field
	 * @param   string  $s_type     The type of the field
	 * @param   string	$s_htmlType	The HTML type
	 */
	public function __construct($s_name,$s_type,$s_htmlType){
		$this->s_name = $s_name;
		$this->s_type	= $s_type;
		$this->setHtmlType($s_htmlType);
	}
}

class HTML_Input extends CoreHTML_Input {
	/**
	 * Generates a new input element
	 *
	 * @param   string  $s_name     The name of the field
	 * @param   string  $s_type     The type of the field (text|password|hidden|email)
	 * @param   string  $s_value    The default text of the field
	 * @param   string	$s_htmlType	The HTML type
	 */
	public function __construct($s_name, $s_type, $s_value, $s_htmlType){
		$a_types	= array('text','hidden','password');
		if( $s_htmlType == 'html5' ){
			$a_types = array_merge($a_types,array('search','email','url','tel','date',
				'month','week','time','color'));
		}
		if( !in_array($s_type,$a_types) ){
			throw new Exception('Invalid input type '.$s_type);
		}
		
		parent::__construct($s_name,$s_type,$s_htmlType);
		$this->setValue($s_value);

		if( $s_htmlType == 'xhtml' ){
			$this->s_tag = '<input type="{type}" name="{name}" {between} value="{value}"/>';
		} 
		else {
			$this->s_tag = '<input type="{type}" name="{name}" {between} value="{value}">';
		}
	}

	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlFormItem::generateItem()
	 * @return  string  The (X)HTML code
	 */
	public function generateItem(){
		$this->s_tag = str_replace('{type}',$this->s_type, $this->s_tag);

		return parent::generateItem();
	}
}

class HTML_Datetime extends CoreHTML_Input {
	public function __construct($s_name,$bo_local){
		$this->s_name = $s_name;
		
		if( $bo_local ){
			$this->s_tag = '<input type="datetime-local" name="{name}" {between}>';
		}
		else {
			$this->s_tag = '<input type="datetime" name="{name}" {between}>';
		}
	}
}

class HTML_Range extends CoreHTML_Input {
	private $i_min;
	private $i_max;
	
	public function __construct($s_name,$s_value){
		parent::__construct($s_name,$s_type,'html5');
		$this->setValue($s_value);
	
		$this->s_tag = '<input type="range" name="{name}"{min}{max}{between} value="{value}">';
	}
	
	public function setMinimun($i_value){
		$this->i_min = $i_value;
	}
	
	public function setMaximun($i_value){
		$this->i_max = $i_value;
	}
	
	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlFormItem::generateItem()
	 * @return  string  The (X)HTML code
	 */
	public function generateItem(){
		( !is_null($this->i_min) ) ? $this->i_min = ' min="'.$this->i_min.'"' : $this->i_min = '';
		( !is_null($this->i_min) ) ? $this->i_max = ' max="'.$this->i_max.'"' : $this->i_max = '';
		
		$this->s_tag = str_replace(array('{min}', '{max}'), array($this->i_min, $this->i_max), $this->s_tag);
	
		return parent::generateItem();
	}
}

class HTML_Date extends HTML_Range {
	public function __construct($s_name,$s_value){
		parent::__construct($s_name,$s_type,'html5');
		$this->setValue($s_value);
	
		$this->s_tag = '<input type="date" name="{name}"{min}{max}{between} value="{value}">';
	}
}

class HTML_Number extends HTML_Range {
	private $i_step;
	
	public function __construct($s_name,$s_value){
		parent::__construct($s_name,$s_type,'html5');
		$this->setValue($s_value);
		
		$this->s_tag = '<input type="number" name="{name}"{min}{max}{step}{between} value="{value}">';
	}
	
	public function setStep($i_step){
		$this->i_step = $i_step;
	}
	
	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlFormItem::generateItem()
	 * @return  string  The (X)HTML code
	 */
	public function generateItem(){
		( !is_null($this->i_step) ) ? $this->i_step = ' step="'.$this->i_step.'"' : $this->i_step = '';
	
		$this->s_tag = str_replace('{step}', $this->i_step, $this->s_tag);
	
		return parent::generateItem();
	}
}

class HTML_Button extends CoreHTML_Input {
	/**
	 * Generates a new button element
	 *
	 * @param string    $s_value    The text on the button
	 * @param string    $s_name     The name of the field, leave empty for no name
	 * @param string    $s_type     The type of the field (button|reset|submit)
	 * @param string	$s_htmlType	The HTML type
	 */
	public function __construct($s_value, $s_name, $s_type, $s_htmlType){
		!empty($s_name) ? $this->s_name = 'name="' . $s_name . '"' : $this->s_name = '';
		
		if( !in_array($s_type,array('button','reset','submit')) ){
			throw new Exception('invalid button type '.$s_type);
		}
		
		parent::__construct($s_name,$s_type,$s_htmlType);
		$this->setValue($s_value);
		
		if( $s_htmlType == 'xhtml' ){
			$this->s_tag = '<input type="{type}" name="{name}" {between} value="{value}"/>';
		} 
		else {
			$this->s_tag = '<input type="{type}" name="{name}" {between} value="{value}">';
		}
	}
}

class HTML_Radio extends CoreHTML_Input {
	protected $bo_checked = false;

	/**
	 * Generates a new radio button element
	 *
	 * @param string	$s_name		The name
	 * @param string	$s_value	The value
	 * @param string	$s_htmlType	The HTML type
	 */
	public function __construct($s_name,$s_value,$s_htmlType){
		$this->s_value = $s_value;
		$this->s_name = $s_name;
		$this->setHtmlType($s_htmlType);
		 
		if( $s_htmlType == 'xhtml' ){
			$this->s_tag = '<input type="radio" name="{name}"{value}{checked} {between}/>';
		} 
		else {
			$this->s_tag = '<input type="radio" name="{name}"{value}{checked} {between}>';
		}
	}

	/**
	 * Disabled
	 */
	public function setValue($s_value){
	}

	/**
	 * Sets the name
	 *
	 * @param       string  $s_name      The value of the radio button
	 */
	public function setName($s_name){
		parent::setValue($s_name);

		return $this;
	}

	/**
	 * Sets the radio button on checked
	 */
	public function setChecked(){
		$this->bo_checked = true;

		return $this;
	}

	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlFormItem::generateItem()
	 * @return  string  The (X)HTML code
	 */
	public function generateItem(){
		($this->bo_checked) ? $s_checked = ' checked="checked"' : $s_checked = '';

		if( !empty($this->s_value) ){
			$this->s_tag	= str_replace('{value}',' value="{value}"',$this->s_tag);
		} 

		$this->s_tag = str_replace('{checked}', $s_checked, $this->s_tag);

		return parent::generateItem();
	}
}

class HTML_Checkbox extends HTML_Radio {
	/**
	 * Generates a new checkbox element
	 *
	 * @param string	$s_name	The name
	 * @param string	$s_value The value
	 * @param string	$s_htmlType	The HTML type
	 */
	public function __construct($s_name,$s_value,$bo_xhtml){
		$this->s_value = $s_value;
		$this->s_name = $s_name;
		$this->setHtmlType($s_htmlType);
		 
		if( $s_htmlType == 'xhtml' ){
			$this->s_tag = '<input type="checkbox" name="{name}"{value}{checked} {between}/>';
		} 
		else {
			$this->s_tag = '<input type="checkbox" name="{name}"{value}{checked} {between}>';
		}
	}

	/**
	 * Sets the name
	 *
	 * @param   string  $s_name      The name of the checkbox
	 */
	public function setName($s_name){
		parent::setValue($s_name);

		return $this;
	}
}
?>