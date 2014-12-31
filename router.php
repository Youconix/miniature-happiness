<?php
interface  Routable {
	public function route($s_command);
}

$s_router	= $_GET['router'];
$a_router	= explode('/',$s_router);

$s_command	= end($a_router);
$s_page		= str_replace('/'.$s_command,'',$s_router);

unset($_GET['router']);
if( array_key_exists('query',$_GET) ){
	$a_query = explode('/',$_GET['query']);
	foreach($a_query AS $s_query){
		$a_parts	= explode('=',$s_query);
		$_GET[$a_parts[0]] = $a_parts[1];
	}
	unset($_GET['query']);
}

$s_page = str_replace(array('http','https','ftp'),array('','',''),$s_page);
while( strpos($s_page,'../') !== false ){
	$s_page	= str_replace('../','',$s_page);
}

define('NIV','./');

if( !file_exists($s_page.'.php') ){
	include('errors/404.php?text=can%20not%20find%20page%20'.$s_page);
	exit();
}

require($s_page.'.php');


$a_class = explode('/',$s_page);
$s_className = UCfirst(end($a_class));

if( !class_exists($s_className) ){
	include('errors/400.php?text=class%20not%20found');
	exit();
}

$obj_class = new $s_className();

if( !is_subclass_of($obj_class,'Routable') ){
	include('errors/500.php?text=class%20is%20not%20routable');
	exit();
}
$_GET['command'] = $s_command;
$obj_class->route($s_command);
?>
