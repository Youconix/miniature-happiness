<?php
define('NIV',dirname(__FILE__).'/../../../');

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}

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

		$this->helper_HTML	= new core\helpers\html\HTML();
		
		$this->s_class = 'defaultClass';
		$this->s_id = 'defaultID';
		$this->s_content = 'lalalala';
	}

	public function tearDown(){
		$this->helper_HTML	= null;

		parent::tearDown();
	}

	/**
	 * Test of calling Div
	 * 
	 * @test
	 */
	public function div(){
		$helper	= $this->helper_HTML->div($this->s_content);
		
		$this->assertTrue(($helper instanceof core\helpers\html\Div));
		
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$s_expected = '<div class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content.'</div>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_Paragraph
	 * 
	 * @test
	 */
	public function paragraph(){
		$helper = $this->helper_HTML->paragraph($this->s_content);
		$this->assertTrue(($helper instanceof core\helpers\html\Paragraph) );
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$s_expected = '<p class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content."</p>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling the list factory
	 * 
	 * @test
	 */
	public function listfactory(){
		$factory = $this->helper_HTML->listFactory();
		
		$this->assertTrue( ($factory instanceof core\helpers\html\ListFactory) );
		
		$obj_list = $factory->numberedList();		 
		$obj_list->setID($this->s_id);
		$obj_list->setClass($this->s_class);
		
		$s_expected = '<ol class="'.$this->s_class.'" id="'.$this->s_id.'">'."</ol>\n";
		$this->assertEquals($s_expected, $obj_list->generateItem());
		
		$obj_list = $factory->uNumberedList();			
		$obj_list->setID($this->s_id);
		$obj_list->setClass($this->s_class);
		
		$s_expected = '<ul class="'.$this->s_class.'" id="'.$this->s_id.'">'."</ul>\n";
		$this->assertEquals($s_expected, $obj_list->generateItem());
		
		$item = $factory->createItem($this->s_content);
		
		$this->assertTrue( ($item instanceof core\helpers\html\ListItem ));
		
		$item->setID($this->s_id);
		$item->setClass($this->s_class);
		 
		$s_expected = '<li class="'.$this->s_class.'" id="'.$this->s_id.'">'.$this->s_content."</li>\n";
		$this->assertEquals($s_expected, $item->generateItem());
	}

	/**
	 * Test for calling HTML_Form
	 * 
	 * @test
	 */
	public function form(){
		$s_link = 'index.php';
		$s_method = 'post';
		
		$helper = $this->helper_HTML->form($s_link,$s_method,true);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Form) );
		
		$helper->setID($this->s_id);

		$s_expected = '<form action="'.$s_link.'" method="'.$s_method.'" enctype="multipart/form-data">'."\n\n</form>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling HTML_Table
	 * 
	 * @test
	 */
	public function table(){
		$factory = $this->helper_HTML->tableFactory();
		
		$this->assertTrue( ($factory instanceof  core\helpers\html\TableFactory ));
		
		$helper = $factory->table();
		$helper->setID($this->s_id);
		$helper->setClass($this->s_class);
		
		$helper->addRow($factory->row());
		
		$s_expected = '<table class="'.$this->s_class.'" id="'.$this->s_id.'">'."\n";
		$s_expected .= "<tbody>\n<tr >\n</tr>\n</tbody>\n</table>\n";
		
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test of calling HTML_TableCell
	 * 
	 * @test
	 */
	public function tableCell(){
		$factory = $this->helper_HTML->tableFactory();
		
		$helper = $factory->row();
		$s_expected	= "<tr >\n";
		for($i=1; $i<=6; $i++){
			$helper->createCell($i);
			
			$s_expected .= "<td >".$i."</td>\n";
		}
		$s_expected .= "</tr>\n";

		$this->assertEquals($s_expected, $helper->generateItem());
	}
	
	/**
	 * Test for calling HTML_Link
	 *
	 * @test
	 */
	 public function link(){
		 $s_url = 'test.php';
		
		 $helper = $this->helper_HTML->link($s_url,$this->s_content);
		
		 $this->assertTrue( ($helper instanceof core\helpers\html\Link) );
		 	
		 $s_expected = '<a href="'.$s_url.'" >'.$this->s_content.'</a>';
		 $this->assertEquals($s_expected, $helper->generateItem());
	 }
	
	 /**
	 * Test for calling HTML_Image
	 *
	 * @test
	 */
	 public function image(){
		 $s_url = 'image.png';
		 $s_alt = $s_title = 'image';
		
		 $helper = $this->helper_HTML->image($s_url);
		
		 $this->assertTrue( ($helper instanceof core\helpers\html\Image) );
		 	
		 $helper->setTitle($s_title);
		 $helper->setvalue($s_alt);
		 	
		 $s_expected = '<img src="'.$s_url.'" title="'.$s_title.'" alt="'.$s_alt.'" >';
		 $this->assertEquals($s_expected, $helper->generateItem());
	 }
	
	 /**
	 * Test for calling HTML_Header
	 *
	 * @test
	 */
	 public function header(){
		 $i_level = 2;
		 $helper = $this->helper_HTML->header($i_level,$this->s_content);
		
		 $this->assertTrue( ($helper instanceof core\helpers\html\Header) );
		 	
		 $s_expected = '<h'.$i_level.' >'.$this->s_content.'</h'.$i_level.'>';
		 $this->assertEquals($s_expected, $helper->generateItem());
	 }

	/**
	 * Test for calling Radio
	 * 
	 * @test
	 */
	public function radio(){
		$s_name = 'radio';
		
		$helper = $this->helper_HTML->radio($s_name,$this->s_content);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Radio) );
		 
		$s_expected = '<input type="radio" name="'.$s_name.'" value="'.$this->s_content.'" >';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Checkbox
	 * 
	 * @test
	 */
	public function checkbox(){
		$s_name = 'checkbox';
		
		$helper = $this->helper_HTML->checkbox($s_name,$this->s_content);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Checkbox) );
		 
		$s_expected = '<input type="checkbox" name="'.$s_name.'" value="'.$this->s_content.'" >';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling StylesheetLink
	 * 
	 * @test
	 */
	public function stylesheetLink(){
		$s_link = 'style.css';
		$s_media = 'screen';
		
		$helper = $this->helper_HTML->stylesheetLink($s_link,$s_media);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\StylesheetLink) );
		 
		$s_expected = '<link rel="stylesheet" href="'.$s_link.'" media="'.$s_media.'" >';
		$this->assertEquals($s_expected, $helper->generateItem()); 
	}

	/**
	 * Test for calling Stylesheet
	 * 
	 * @test
	 */
	public function stylesheet(){
		$s_css = 'body { color#FFF; }';
		 
		$helper = $this->helper_HTML->stylesheet($s_css);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Stylesheet) );
		 
		$s_expected = '<style>'."\n<!--\n".$s_css."\n-->\n</style>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling JavascriptLink
	 * 
	 * @test
	 */
	public function javascriptLink(){
		$s_link = 'javascript.js';
		$helper = $this->helper_HTML->javascriptLink($s_link);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\JavascriptLink) );
		 
		$s_expected = '<script src="'.$s_link.'" >'."</script>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Javascript
	 * 
	 * @test
	 */
	public function testJavascript(){
		$s_javascript = 'alert("hi");';
		 
		$helper = $this->helper_HTML->javascript($s_javascript);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Javascript) );
		 
		$s_expected = '<script>'."\n<!--\n".$s_javascript."\n//-->\n</script>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Metatag
	 * 
	 * @test
	 */
	public function metatag(){
		$s_name = 'meta';
		 
		$helper = $this->helper_HTML->metatag($s_name, $this->s_content);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Metatag) );
		 
		$s_expected = '<meta name="'.$s_name.'" content="'. $this->s_content.'" >'."\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Span
	 * 
	 * @test
	 */
	public function span(){
		$helper = $this->helper_HTML->span($this->s_content);
		
		$this->assertTrue( ($helper instanceof  core\helpers\html\Span) );
		 
		$s_expected = '<span >'.$this->s_content.'</span>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Audio
	 * HTML 5 only
	 * 
	 * @test
	 */
	public function audio(){
		$s_url = 'audio.ogg';
		$s_type = 'ogg';
		
		$helper = $this->helper_HTML->audio($s_url,$s_type);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Audio) );
		 
		$helper->autoplay(true);
		$helper->controls(true);
		$helper->loop(false);		
		
		$s_expected = '<audio autoplay="autoplay" controls="controls"><source src="'.$s_url.'" type="video/'.$s_type.'"></audio>' ;
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Video
	 * HTML 5 only
	 * 
	 * @test
	 */
	public function video(){
		$s_url = 'video.webm';
		$s_type = 'WebM';
		
		$helper = $this->helper_HTML->video($s_url,$s_type);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Video) );
		 
		$helper->autoplay(true);
		$helper->controls(true);
		$helper->loop(false);		
		
		$s_expected = '<video autoplay="autoplay" controls="controls"><source src="'.$s_url.'" type="video/'.$s_type.'"></video>' ;
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Canvas
	 * HTML 5 only
	 * 
	 * @test
	 */
	public function canvas(){
		$helper = $this->helper_HTML->canvas();
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Canvas) );
		 
		$s_expected = '<canvas ></canvas>';
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling PageHeader
	 * HTML 5 only
	 * 
	 * @test
	 */
	public function pageHeader(){
		$helper = $this->helper_HTML->pageHeader($this->s_content);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\PageHeader) );
				 
		$s_expected = '<header >'."\n".$this->s_content."\n</header>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Footer
	 * HTML 5 only
	 * 
	 * @test
	 */
	public function pageFooter(){
		$helper = $this->helper_HTML->pageFooter($this->s_content);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Footer) );
		 
		$s_expected = '<footer >'."\n".$this->s_content."\n</footer>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}

	/**
	 * Test for calling Nav
	 * HTML 5 only
	 * 
	 * @test
	 */
	public function navigation(){		
		$helper = $this->helper_HTML->navigation($this->s_content);
		
		$this->assertTrue( ($helper instanceof core\helpers\html\Nav) );
		
		$s_expected = '<nav >'."\n".$this->s_content."\n</nav>\n";
		$this->assertEquals($s_expected, $helper->generateItem());
	}
}
?>