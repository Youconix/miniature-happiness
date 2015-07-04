<?php
define('NIV', '../../');

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
        require (NIV . 'core/Memory.php');
        \core\Memory::startUp();
        
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
            $s_text .= '"' . $s_name . '" : "' . t($s_key) . '"' . ",\n";
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