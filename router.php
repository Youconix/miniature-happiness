<?php
interface  Routable {
	public function route($s_command);
}

function lookup($s_router){
	if( !file_exists(NIV.'routes.php') ){
		return null;
	}
	require(NIV.'routes.php');
	if( !array_key_exists($s_router,$a_routes) ){
		return null;
	}
	$a_data	= $a_routes[$s_router];
    
	if( array_key_exists('query',$a_data) ){
		populateQuery($a_data['query']);
	}
	
	return array('page'=>$a_data['router'],'command'=>$a_data['command']);
}

function translate($s_router){
	$a_router	= explode('/',$s_router);

	$s_command	= end($a_router);
	$s_page		= str_replace('/'.$s_command,'',$s_router);


	if( array_key_exists('query',$_GET) ){
		populateQuery($_GET['query']);
		unset($_GET['query']);
	}

	$s_page = str_replace(array('http','https','ftp'),array('','',''),$s_page);
	while( strpos($s_page,'../') !== false ){
		$s_page	= str_replace('../','',$s_page);
	}
	
	return array('page'=>$s_page,'command'=>$s_command);
}

function populateQuery($s_query){
	$a_query = explode('/',$s_query);
	foreach($a_query AS $s_query){
		$a_parts	= explode('=',$s_query);
		$_GET[$a_parts[0]] = $a_parts[1];
	}
}

define('NIV','./');

$s_router	= $_GET['router'];
if( substr($s_router,-1) == '/' ){
  $s_router = substr($s_router,0,-1);
}

$a_data = lookup($s_router);
if( is_null($a_data) ){
  $a_data = translate($s_router);
}

$s_page = $a_data['page'];
$s_command = $a_data['command'];
unset($_GET['router']);

if( !file_exists($s_page.'.php') ){
	$_SESSION['error'] = 'can not find page '.$s_page;
	include('errors/404.php');
	exit();
}


$_GET['command'] = $s_command;
$_SERVER['SCRIPT_NAME'] = $s_page.'.php';

require($s_page.'.php');


$a_class = explode('/',$s_page);
$s_className = UCfirst(end($a_class));

if( !class_exists($s_className) ){
	$_SESSION['error'] = 'class '.$s_className.' not found.';
	include('errors/400.php');
	exit();
}

$obj_class = new $s_className();

if( !is_subclass_of($obj_class,'Routable') ){
	$_SESSION['error'] = 'class '.$s_className.' is not routable';
	include('errors/500.php');
	exit();
}

$obj_class->route($s_command);
?>
