<?php

abstract class GeneralJS
{

    protected $service_Language;

    protected $s_styleDir;

    public function __construct()
    {
        require (NIV . 'core/Memory.php');
        \core\Memory::startUp();
        
        $this->service_Language = \core\Memory::services('Language');
        
        $this->getStyleDir();
        
        $this->sendHeaders();
        
        $this->display();
    }

    protected function getStyleDir()
    {
        $s_templateDir = \core\Memory::models('Config')->getStylesDir();
        $service_Cookie = \core\Memory::services('Cookie');
        if ($service_Cookie->exists('private_style_dir')) {
            $s_styleDir = $this->clearLocation($service_Cookie->get('private_style_dir'));
            if ($this->service_File->exists(NIV . 'styles/' . $s_styleDir . '/templates/layouts')) {
                $s_templateDir = $s_styleDir;
            } else {
                $service_Cookie->delete('private_style_dir', '/');
            }
        }
        $this->s_styleDir = NIV . 'styles/' . $s_templateDir . '/';
    }

    /**
     * Clears the location path from evil input
     *
     * @param string $s_location            
     * @return string path
     */
    private function clearLocation($s_location)
    {
        while ((strpos($s_location, './') !== false) || (strpos($s_location, '../') !== false)) {
            $s_location = str_replace(array(
                './',
                '../'
            ), array(
                '',
                ''
            ), $s_location);
        }
        
        return $s_location;
    }

    protected function sendHeaders()
    {
        header('Content-Type: text/javascript');
        header('Expires: Thu, ' . date('d-M-y', (time() + 604800)) . ' 00:00:01 GMT'); // cache for one week
    }

    abstract protected function display();
}
?>
