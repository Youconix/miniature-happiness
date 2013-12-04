<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testHTML extends GeneralTest {
	private $helper_HTML;
	private $s_class;
	private $s_id;
	private $s_content;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/helpers/HTML.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->helper_HTML	= new Helper_HTML();
		$this->helper_HTML->setHtmlType('xhtml');
		
		$this->s_class = 'defaultClass';
		$this->s_id = 'defaultID';
		$this->s_content = 'lalalala';
	}

	public function tearDown(){
		$this->helper_HTML	= null;

		parent::tearDown();
	}

	/**
	 * Test of calling HTML_Div
	 */
	public function testDiv() {
		$helper	= $this->helper_HTML->div($this->s_content);
		$this->assertTrue(($helper instanceof HTML_Div));
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$s_expected = '<div class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content.'</div>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_Paragraph
	 */
	public function testParagraph() {
		$helper = $this->helper_HTML->paragraph($this->s_content);
		$this->assertTrue(($helper instanceof HTML_Paragraph) );
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$s_expected = '<p class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content."</p>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_Textarea
	 */
	public function testTextarea() {
		$helper = $this->helper_HTML->textarea('message',$this->s_content);
		$this->assertTrue( ($helper instanceof HTML_Textarea) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$s_expected = '<textarea rows="0" cols="0" name="message" class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content."</textarea>";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_UnList
	 */
	public function testUnList() {
		$helper = $this->helper_HTML->unList(true);
		
		$this->assertTrue( ($helper instanceof HTML_UnList) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$s_expected = '<ol class="'.$this->s_class.'" id="'.$this->s_id.'">'."</ol>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_ListItem
	 */
	public function testListItem() {
		$helper = $this->helper_HTML->listItem($this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_ListItem ));
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<li class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content."</li>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Form
	 */
	public function testForm(){
		$s_link = 'index.php';
		$s_method = 'post';
		
		$helper = $this->helper_HTML->form($s_link,$s_method,true);
		
		$this->assertTrue( ($helper instanceof HTML_Form) );
		
		$helper->setID($this->s_id);

		$s_expected = '<form action="'.$s_link.'" method="'.$s_method.'" enctype="multipart/form-data">'."\n\n</form>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Table
	 */
	public function testTable() {
		$helper = $this->helper_HTML->table();
		
		$this->assertTrue( ($helper instanceof  HTML_Table ));
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$helper->addHeaderCell('head');
		$helper->addFooterCell('foot');
		$helper->addRow($this->helper_HTML->tableRow());
		
		$s_expected = '<table class="'.$this->s_class.'" id="'.$this->s_id.'">'."\n";
		$s_expected .= "<thead>\n<tr>\n<td>head</td>\n</tr>\n</thead>\n";
		$s_expected .= "<tfoot>\n<tr>\n<td>foot</td>\n</tr>\n</tfoot>\n";
		$s_expected .= "<tbody>\n<tr >\n\n</tr>\n</tbody>\n</table>\n";
		
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_TableRow
	 */
	public function testTableRow() {
		$helper = $this->helper_HTML->tableRow();
		
		$this->assertTrue( ($helper instanceof HTML_TableRow) );

		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		$helper->setValue('test');
		
		$s_expected = '<tr class="'.$this->s_class.'" id="'.$this->s_id.'">'."\n<td >test</td>\n</tr>";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_TableCell
	 */
	public function testTableCell() {
		$helper = $this->helper_HTML->tableCell();
		
		$this->assertTrue( ($helper instanceof HTML_TableCell) );

		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		$helper->setValue('test');
		
		$s_expected = '<td class="'.$this->s_class.'" id="'.$this->s_id.'">'."test</td>";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_Input
	 */
	public function testInput() {
		$s_name = 'inputTest';
		$s_type = "password";
		
		$helper = $this->helper_HTML->input($s_name, $s_type,$this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Input) );
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<input type="password" name="'.$s_name.'" class="'.$this->s_class.'" id="'.$this->s_id.'" value="'.$this->s_content.'"/>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_Button
	 */
	public function testButton() {
		$s_name = 'inputTest';
		$s_type = "submit";
		
		$helper = $this->helper_HTML->button($this->s_content,$s_name, $s_type);
		
		$this->assertTrue( ($helper instanceof HTML_Button) );
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<input type="submit" name="'.$s_name.'" class="'.$this->s_class.'" id="'.$this->s_id.'" value="'.$this->s_content.'"/>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Link
	 */
	public function testLink() {
		$s_url = 'test.php';
		
		$helper = $this->helper_HTML->link($s_url,$this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Link) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<a href="'.$s_url.'" class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content.'</a>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Image
	 */
	public function testImage() {
		$s_url = 'image.png';
		$s_alt = $s_title = 'image';
		
		$helper = $this->helper_HTML->image($s_url);
		
		$this->assertTrue( ($helper instanceof HTML_Image) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		$helper->setTitle($s_title);
		$helper->setvalue($s_alt);
		 
		$s_expected = '<img src="'.$s_url.'" title="'.$s_title.'" alt="'.$s_alt.'" class="'.$this->s_class.'" id="'.$this->s_id.'"/>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Header
	 */
	public function testHeader(){
		$i_level = 2;
		$helper = $this->helper_HTML->header($i_level,$this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Header) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<h'.$i_level.' class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content.'</h'.$i_level.'>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Radio
	 */
	public function testRadio() {
		$s_name = 'radio';
		
		$helper = $this->helper_HTML->radio($s_name,$this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Radio) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<input type="radio" name="'.$s_name.'" value="'.$this->s_content.'" class="'.$this->s_class.'" id="'.$this->s_id.'"/>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Checkbox
	 */
	public function testCheckbox() {
		$s_name = 'checkbox';
		
		$helper = $this->helper_HTML->checkbox($s_name,$this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Checkbox) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<input type="checkbox" name="'.$s_name.'" value="'.$this->s_content.'" class="'.$this->s_class.'" id="'.$this->s_id.'"/>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Select
	 */
	public function testSelect() {
		$s_name	= 'select';
		$a_values = array(1,2,3,4,5,6);
		
		$helper = $this->helper_HTML->select($s_name);
		
		$this->assertTrue( ($helper instanceof HTML_Select) );
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		$s_expected = '<select name="'.$s_name.'" class="'.$this->s_class.'" id="'.$this->s_id.'">'."\n";
		
		foreach($a_values AS $i_value){
			$helper->setOption($i_value, false);
			$s_expected .= '<option>'.$i_value."</option>\n";
		}
		$s_expected .= "</select>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_StylesheetLink
	 */
	public function testStylesheetLink() {
		$s_link = 'style.css';
		$s_media = 'screen';
		
		$helper = $this->helper_HTML->stylesheetLink($s_link,$s_media);
		
		$this->assertTrue( ($helper instanceof HTML_StylesheetLink) );
		$helper->setID($this->s_id);
		 
		$s_expected = '<link rel="stylesheet" href="'.$s_link.'" type="text/css" media="'.$s_media.'" id="'.$this->s_id.'"/>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Stylesheet
	 */
	public function testStylesheet() {
		$s_css = 'body { color#FFF; }';
		 
		$helper = $this->helper_HTML->stylesheet($s_css);
		
		$this->assertTrue( ($helper instanceof HTML_Stylesheet) );
		 
		$s_expected = '<style type="text/css">'."\n<!--\n".$s_css."\n-->\n</style>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_JavascriptLink
	 */
	public function testJavascriptLink() {
		$s_link = 'javascript.js';
		$helper = $this->helper_HTML->javascriptLink($s_link);
		
		$this->assertTrue( ($helper instanceof HTML_JavascriptLink) );
		
		$helper->setID($this->s_id);
		 
		$s_expected = '<script src="'.$s_link.'" type="text/javascript" id="'.$this->s_id.'">'."</script>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Javascript
	 */
	public function testJavascript() {
		$s_javascript = 'alert("hi");';
		 
		$helper = $this->helper_HTML->javascript($s_javascript);
		
		$this->assertTrue( ($helper instanceof HTML_Javascript) );
		 
		$s_expected = '<script type="text/javascript">'."\n<!--\n".$s_javascript."\n//-->\n</script>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Metatag
	 */
	public function testMetatag() {
		$s_name = 'meta';
		 
		$helper = $this->helper_HTML->metatag($s_name, $this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Metatag) );
		
		$helper->setID($this->s_id);
		 
		$s_expected = '<meta name="'.$s_name.'" content="'. $this->s_content.'" id="'.$this->s_id.'"/>'."\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Span
	 */
	public function testSpan() {
		$helper = $this->helper_HTML->span($this->s_content);
		
		$this->assertTrue( ($helper instanceof  HTML_Span) );
		 
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<span class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content.'</span>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Audio
	 * HTML 5 only
	 */
	public function testAudio(){
		$s_url = 'audio.ogg';
		$s_type = 'ogg';
		
		try {
			$this->helper_HTML->audio("");
			
			$this->fail("Expected Exception");
		}
		catch(Exception $e){}
		
		$this->helper_HTML->setHTML5();
		$helper = $this->helper_HTML->audio($s_url,$s_type);
		
		$this->assertTrue( ($helper instanceof HTML_Audio) );
		 
		$helper->setID($this->s_id);
		$helper->autoplay(true);
		$helper->controls(true);
		$helper->loop(false);		
		
		$s_expected = '<audio autoplay="autoplay" controls="controls" id="'.$this->s_id.'"><source src="'.$s_url.'" type="video/'.$s_type.'" /></audio>' ;
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Video
	 * HTML 5 only
	 */
	public function testVideo(){
		$s_url = 'video.webm';
		$s_type = 'WebM';
		
		try {
			$this->helper_HTML->video("");
			
			$this->fail("Expected Exception");
		}
		catch(Exception $e){}
		
		$this->helper_HTML->setHTML5();
		$helper = $this->helper_HTML->video($s_url,$s_type);
		
		$this->assertTrue( ($helper instanceof HTML_Video) );
		 
		$helper->setID($this->s_id);
		$helper->autoplay(true);
		$helper->controls(true);
		$helper->loop(false);		
		
		$s_expected = '<video autoplay="autoplay" controls="controls" id="'.$this->s_id.'"><source src="'.$s_url.'" type="video/'.$s_type.'" /></video>' ;
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Canvas
	 * HTML 5 only
	 */
	public function testCanvas(){
		try {
			$this->helper_HTML->canvas();
			
			$this->fail("Expected Exception");
		}
		catch(Exception $e){}
		
		$this->helper_HTML->setHTML5();
		$helper = $this->helper_HTML->canvas();
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<canvas class="'.$this->s_class.'" id="'.$this->s_id.'"></canvas>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_PageHeader
	 * HTML 5 only
	 */
	public function testPageHeader(){
		try {
			$this->helper_HTML->pageHeader("");
			
			$this->fail("Expected Exception");
		}
		catch(Exception $e){}
		
		$this->helper_HTML->setHTML5();
		$helper = $this->helper_HTML->pageHeader($this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_PageHeader) );
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<header class="'.$this->s_class.'" id="'.$this->s_id.'">'."\n".$this->s_content."\n</header>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Footer
	 * HTML 5 only
	 */
	public function testPageFooter(){
		try {
			$this->helper_HTML->pageFooter();
			
			$this->fail("Expected Exception");
		}
		catch(Exception $e){}
		
		$this->helper_HTML->setHTML5();
		$helper = $this->helper_HTML->pageFooter($this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Footer) );
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<footer class="'.$this->s_class.'" id="'.$this->s_id.'">'."\n".$this->s_content."\n</footer>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Nav
	 * HTML 5 only
	 */
	public function testNavigation(){
		try {
			$this->helper_HTML->navigation("");
			
			$this->fail("Expected Exception");
		}
		catch(Exception $e){}
		
		$this->helper_HTML->setHTML5();
		$helper = $this->helper_HTML->navigation($this->s_content);
		
		$this->assertTrue( ($helper instanceof HTML_Nav) );
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		 
		$s_expected = '<nav class="'.$this->s_class.'" id="'.$this->s_id.'">'."\n".$this->s_content."\n</nav>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}
}
?>