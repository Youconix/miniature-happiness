<?php
define('NIV',dirname(__FILE__).'/../../../../');

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}

class testInput extends GeneralTest {
	private $inputFactory;
	private $s_name = 'defaultName';
	private $s_value = 'defaultValue';
	private $s_htmlType = 'html5';

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/helpers/HTML.inc.php');
		require_once(NIV.'include/helpers/html/Input.php');
	}

	public function setUp(){
		parent::setUp();

		$this->inputFactory	= core\helpers\html\InputFactory::getInstance();
	}

	public function tearDown(){
		$this->inputFactory	= null;

		parent::tearDown();
	}
	
	/**
	 * Tests calling the input object
	 * 
	 * @test
	 */
	public function input(){
		$object = $this->inputFactory->input($this->s_name,'text',$this->s_value,$this->s_htmlType);
		
		$this->assertTrue( ($object instanceof core\helpers\html\Input));
	}
	
	/**
	 * Tests calling the range input object
	 * 
	 * @test
	 */
	public function range(){
		$i_value = 13;
		$i_min	= 0;
		$i_max	= 20;
		
		$object = $this->inputFactory->range($this->s_name,$i_value);
		
		$this->assertTrue( ($object instanceof core\helpers\html\Range));
		
		$object->setMinimun($i_min);
		$object->setMaximun($i_max);
		
		$s_expected = '<input type="range" name="'.$this->s_name.'" min="'.$i_min.'" max="'.$i_max.'" value="'.$i_value.'">';
		$this->assertEquals($s_expected,$object->generateItem());
	}
	
	/**
	 * Tests calling the number input object
	 * 
	 * @test
	 */
	public function number(){
		$i_value = 13;
		$i_min	= 0;
		$i_max	= 20;
		$i_step = 0.5;
		
		$object = $this->inputFactory->number($this->s_name,$i_value);
		
		$this->assertTrue( ($object instanceof core\helpers\html\Number));
		
		$object->setMinimun($i_min);
		$object->setMaximun($i_max);
		$object->setStep($i_step);
		$s_expected = '<input type="number" name="'.$this->s_name.'" min="'.$i_min.'" max="'.$i_max.'" step="'.$i_step.'" value="'.$i_value.'">';
		$this->assertEquals($s_expected,$object->generateItem());
	}
	
	/**
	 * Tests calling the date input object
	 *
	 * @test
	 */
	public function date(){
		$i_value = 13;
		$i_min	= 0;
		$i_max	= 20;
	
		$object = $this->inputFactory->date($this->s_name,$i_value);
	
		$this->assertTrue( ($object instanceof core\helpers\html\Date));
	
		$object->setMinimun($i_min);
		$object->setMaximun($i_max);
	
		$s_expected = '<input type="date" name="'.$this->s_name.'" min="'.$i_min.'" max="'.$i_max.'" value="'.$i_value.'">';
		$this->assertEquals($s_expected,$object->generateItem());
	}
	
	/**
	 * Tests calling the datetime input object
	 * 
	 * @test
	 */
	public function datetime(){
		$object = $this->inputFactory->datetime($this->s_name,false);
		
		$this->assertTrue( ($object instanceof core\helpers\html\Datetime));
    
    $s_expected = '<input type="datetime" name="'.$this->s_name.'" >';
		$this->assertEquals($s_expected,$object->generateItem());
	}
	
	/**
	 * Tests creating a new button
	 * 
	 * @test
	 */
	public function button(){
		$object = $this->inputFactory->button($this->s_name,'reset',$this->s_value,$this->s_htmlType);
		
		$this->assertTrue( ($object instanceof core\helpers\html\Button));
    
    $s_expected = '<input type="reset" name="'.$this->s_name.'"  value="'.$this->s_value.'">';
    $this->assertEquals($s_expected,$object->generateItem());
	}
	
	/**
	 * Test creating a radio button
	 * 
	 * @test
	 */
	public function radio(){
		$object = $this->inputFactory->radio($this->s_name,$this->s_value,$this->s_htmlType);
		
		$this->assertTrue( ($object instanceof core\helpers\html\Radio));
    
    $s_expected = '<input type="radio" name="'.$this->s_name.'" value="'.$this->s_value.'" >';
		$this->assertEquals($s_expected,$object->generateItem());
		
		$object = $this->inputFactory->radio($this->s_name,$this->s_value,$this->s_htmlType);
		$object->setChecked();
    $s_expected = '<input type="radio" name="'.$this->s_name.'" value="'.$this->s_value.'" checked="checked" >';
		$this->assertEquals($s_expected,$object->generateItem());
	}
	
	/**
	 * Test creating a checkbox
	 *
	 * @test
	 */
	public function checkbox(){
		$object = $this->inputFactory->checkbox($this->s_name,$this->s_value,$this->s_htmlType);
	
		$this->assertTrue( ($object instanceof core\helpers\html\Checkbox));
    
    $s_expected = '<input type="checkbox" name="'.$this->s_name.'" value="'.$this->s_value.'" >';
		$this->assertEquals($s_expected,$object->generateItem());
	
		$object = $this->inputFactory->checkbox($this->s_name,$this->s_value,$this->s_htmlType);
		$object->setChecked();
    $s_expected = '<input type="checkbox" name="'.$this->s_name.'" value="'.$this->s_value.'" checked="checked" >';
		$this->assertEquals($s_expected,$object->generateItem());
	}

	/**
	 * Test of calling Textarea
	 * 
	 * @test
	 */
	public function textarea(){
		$helper = $this->inputFactory->textarea('message',$this->s_content);
		$this->assertTrue( ($helper instanceof core\helpers\html\Textarea) );
		
		$s_expected = '<textarea rows="0" cols="0" name="message" >'.$this->s_content."</textarea>";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Select
	 * 
	 * @test
	 */
	public function select(){
		$s_name	= 'select';
		$a_values = array(1,2,3,4,5,6);
		
		$helper = $this->inputFactory->select($s_name);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Select) );
		
		$s_expected = '<select name="'.$s_name.'" >'."\n";
		
		foreach($a_values AS $i_value){
			$helper->setOption($i_value, false);
			$s_expected .= '<option>'.$i_value."</option>\n";
		}
		$s_expected .= "</select>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}
}