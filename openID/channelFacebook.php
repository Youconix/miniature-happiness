<?php
/* Get data */
define('NIV','../');
define('PROCESS','1');
define("DATA_DIR","../../data/");

require(NIV.'include/Memory.php');
Memory::startUp();

$s_locale	= Memory::services('Language')->get('language/locale');

$i_cacheExpire = 60*60*24*365;
header("Pragma: public");
header("Cache-Control: max-age=".$i_cacheExpire);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$i_cacheExpire) . ' GMT');
echo('<script src="//connect.facebook.net/'.$s_locale.'/all.js"></script>');

Memory::endProgram();
?>