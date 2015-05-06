<?php
define('NIV','../');
require_once (NIV . 'core/Memory.php');
require (NIV . 'tests/GeneralTest.php');

$_SERVER['DOCUMENT_ROOT'] = NIV;

if (! defined('DATA_DIR')) {
    define('DATA_DIR', NIV . 'admin/data/');
}
if (! defined('LEVEL')) {
    define('LEVEL', NIV);
}

$_SERVER['HTTP_HOST'] = 'unittesting';

/* First run for inclusion */
\core\Memory::setTesting();
echo "Strapped up and ready.\n";