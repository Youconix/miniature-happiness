<?php
define('NIV','../');
define('DATA_DIR', NIV.'tests/admin/data/');

require_once (NIV . 'core/Memory.php');
require (NIV . 'tests/GeneralTest.php');

$_SERVER['REQUEST_URI'] = "PHPUNIT";
$_SERVER['HTTP_USER_AGENT'] = "PHPUNIT";
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
error_reporting(E_ALL & ~E_DEPRECATED);
echo "Strapped up and ready.\n";