<?php
if (! class_exists('\core\services\Service')) {
    require (NIV . 'core/services/Service.inc.php');
}
if (! class_exists('\core\services\Settings')) {
    require_once (NIV . 'core/services/Settings.inc.php');
}

class DummySettings extends \core\services\Settings
{

    private $a_values = array();

    public function __construct()
    {}

    public function setValue($s_path, $s_value)
    {
        $s_path = $this->preparePath($s_path);
        $this->a_values[$s_path] = $s_value;
    }

    public function get($s_path)
    {        
        $s_path = $this->preparePath($s_path);
        
        return $this->a_values[$s_path];
    }

    private function preparePath($s_path)
    {
        $i_pos = strpos($s_path, '/');
        if (($i_pos === false) || (substr($s_path, 0, $i_pos) != 'settings')) {
            $s_path = 'settings/' . $s_path;
        }
        
        return str_replace('/', '_', $s_path);
    }
    
    /**
     * Checks of the given part of the loaded file exists
     *
     * @param String $s_path
     *            The path to the language-part
     * @return boolean, true if the part exists otherwise false
     */
    public function exists($s_path)
    {
        return (array_key_exists($s_path, $this->a_values));
    }
}
?>