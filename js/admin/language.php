<?php
define('NIV', '../../');

class AdminGroups {
    private $a_items = array();
    
    public function __construct()
    {
        require (NIV . 'core/Memory.php');
        \core\Memory::startUp();
    
        $this->service_Language = \core\Memory::services('Language');
        
        $this->getItems();
    
        $this->display();
    }
    
    private function getItems(){ 
        $service_QueryBuilder = \core\Memory::services('QueryBuilder')->createBuilder();
        $service_QueryBuilder->select('language_admin','javascript,language');
        $database = $service_QueryBuilder->getResult();
        
        if( $database->num_rows() > 0 ){
            $a_data = $database->fetch_assoc();
            foreach( $a_data AS $a_item ){
                $this->a_items[ $a_item['javascript'] ] = $a_item['language'];
            }
        }
    }
    

    protected function display()
    {
        $s_text = "var languageAdmin = { \n";
        foreach( $this->a_items AS $s_name => $s_key ){
            $s_text .= '"'.$s_name.'" : "'.t($s_key).'"'.",\n";
        }  
        $s_text .= '};' ;
        
        $service_Headers = \core\Memory::services('Headers');
        
        $service_Headers->setJavascript();
        $service_Headers->cache(-1);
        $service_Headers->printHeaders();
        echo($s_text);
    }
}

$obj_AdminGroups = new AdminGroups();
?>