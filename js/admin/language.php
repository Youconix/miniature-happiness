<?php
define('NIV', '../../');

class AdminLanguage
{

    /**
     * @var \core\services\Language
     */
    private $service_Language;
    /**
     * @var \core\services\Builder
     */
    private $service_Builder;
    private $a_items = array();

    public function __construct()
    {
        require (NIV . 'core/Memory.php');
        \core\Memory::startUp();
        
        $this->service_Language = \Loader::inject('\core\services\Language');
        $this->service_Builder = \Loader::inject('\core\services\QueryBuilder')->createBuilder();
        
        $this->getItems();
        
        $this->display();
    }

    private function getItems()
    {
        $this->service_Builder->select('language_admin', 'javascript,language');
        $database = $this->service_Builder->getResult();
        
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
        
        $service_Headers = \Loader::inject('\core\services\Headers');
        
        $service_Headers->setJavascript();
        $service_Headers->cache(- 1);
        $service_Headers->printHeaders();
        echo ($s_text);
    }
}

$obj_AdminLanguage = new AdminLanguage();
?>