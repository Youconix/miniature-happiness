<?php
define('NIV', '../../');
define('DS',DIRECTORY_SEPARATOR);
require_once(NIV.DS.'vendor'.DS.'youconix'.DS.'core'.DS.'bootstrap.inc.php');

class AdminLanguage
{

    /**
     * @var \Language
     */
    private $service_Language;
    /**
     * @var \Builder
     */
    private $builder;
    private $a_items = array();

    public function __construct()
    {        
        $this->service_Language = \Loader::inject('\Language');
        $this->builder = \Loader::inject('\Builder');
        
        $this->getItems();
        
        $this->display();
    }

    private function getItems()
    {
        $this->builder->select('language_admin', 'javascript,language');
        $database = $this->builder->getResult();
        
        if ($database->num_rows() > 0) {
            $a_data = $database->fetch_assoc();
            foreach ($a_data as $a_item) {
                $this->a_items[$a_item['javascript']] = $a_item['language'];
            }
        }
    }

    protected function display()
    {
        $s_text = "var languageAdmin = { \n";
        foreach ($this->a_items as $s_name => $s_key) {
        	$s_item = t($s_key);
        	$s_item = str_replace(array("\n","\t"),array('',' '),$s_item);
        	while( strpos($s_item,'  ') !== false ){
        		$s_item = str_replace('  ',' ',$s_item);
        	}
        	        	
            $s_text .= '"' . $s_name . '" : "' . $s_item . '"' . ",\n";
        }
        $s_text .= '};';
        
        $service_Headers = \Loader::inject('\Headers');
        
        $service_Headers->setJavascript();
        $service_Headers->cache(- 1);
        $service_Headers->printHeaders();
        echo ($s_text);
    }
}

$obj_AdminLanguage = new AdminLanguage();
?>