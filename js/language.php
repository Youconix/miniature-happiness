<?php
define('NIV', '../');

class SiteLanguage
{

    /**
     * @var \Language
     */
    private $service_Language;
    /**
     * @var \core\services\Builder
     */
    private $service_Builder;
    private $a_items = array();
    private $a_widgetItems = array();

    public function __construct()
    {
        require (NIV . 'core/Memory.php');
        \core\Memory::startUp();
        
        $this->service_Language = \Loader::inject('\Language');
        $this->service_Builder = \Loader::inject('\Builder');
        
        $this->getSiteItems();
        
        $this->getWidgetItems();
        
        $this->display();
    }

    private function getSiteItems()
    {
        $this->service_Builder->select('language_site', 'javascript,language');
        $database = $this->service_Builder->getResult();
        
        if ($database->num_rows() > 0) {
            $a_data = $database->fetch_assoc();
            foreach ($a_data as $a_item) {
                $this->a_items[$a_item['javascript']] = $a_item['language'];
            }
        }
    }
    
    private function getWidgetItems()
    {
        $this->service_Builder->select('language_widgets', 'javascript,language');
        $database = $this->service_Builder->getResult();

        if ($database->num_rows() > 0) {
            $a_data = $database->fetch_assoc();
            foreach ($a_data as $a_item) {
                $this->a_widgetItems[$a_item['javascript']] = $a_item['language'];
            }
        }
    }

    protected function display()
    {
        $s_text = "var languageSite = { \n";
        foreach ($this->a_items as $s_name => $s_key) {
            $s_text .= '"' . $s_name . '" : "' . $this->getText($s_key) . '"' . ",\n";
        }
        $s_text .= "};\n";
        $s_text .= "var languageWidgets = { \n";
        foreach ($this->a_widgetItems as $s_name => $s_key) {
            $s_text .= '"' . $s_name . '" : "' . $this->getText($s_key) . '"' . ",\n";
        }
        $s_text .= '};';
        
        $service_Headers = \Loader::inject('\Headers');
        
        $service_Headers->setJavascript();
        $service_Headers->cache(- 1);
        $service_Headers->printHeaders();
        echo ($s_text);
    }
    
    protected function getText($s_key){
    	$s_item = t($s_key);
    	$s_item = str_replace(array("\n","\t"),array('',' '),$s_item);
    	while( strpos($s_item,'  ') !== false ){
    		$s_item = str_replace('  ',' ',$s_item);
    	}
    	
    	return $s_item;
    }
}

$obj_SiteLanguage = new SiteLanguage();
?>