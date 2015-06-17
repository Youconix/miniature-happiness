<?php
namespace tests;

abstract class GeneralTest extends \PHPUnit_Framework_TestCase
{

    protected $s_base;

    protected $s_temp;

    public function __construct()
    {
        parent::__construct();

        /* First run for inclusion */
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

    // This function should now no longer be used, as far as we know. - Roxanna.
/*     protected function loadStub($s_name)
    {
        if (! class_exists($s_name)) {
            require (NIV . 'tests/stubs/' . $s_name . '.php');
        }
    } */
}
