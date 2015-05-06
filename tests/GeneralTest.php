<?php

abstract class GeneralTest extends PHPUnit_Framework_TestCase
{

    protected $s_base;

    protected $s_temp;

    public function __construct()
    {
        require_once (NIV . 'core/Memory.php');
        $_SERVER['DOCUMENT_ROOT'] = NIV;
        parent::__construct();
        
        if (! defined('DATA_DIR')) {
            define('DATA_DIR', NIV . 'admin/data/');
        }
        if (! defined('LEVEL')) {
            define('LEVEL', NIV);
        }
        
        $_SERVER['HTTP_HOST'] = 'unittesting';
        
        /* First run for inclusion */
        \core\Memory::setTesting();
        $this->s_base = '/';
        
        error_reporting(E_ALL);
        ini_set('display_errors', 'on');
        
        $this->s_temp = sys_get_temp_dir();
        if ((substr($this->s_temp, - 1) != '/') && (substr($this->s_temp, - 1) != '\\')) {
            $this->s_temp .= '/';
        }
    }

    protected function setUp()
    {
        \core\Memory::setTesting();
        
        ob_start();
    }

    protected function tearDown()
    {
        \core\Memory::reset();
        
        ob_flush();
    }

    protected function loadStub($s_name)
    {
        if (! class_exists($s_name)) {
            require (NIV . 'tests/stubs/' . $s_name . '.php');
        }
    }
}
