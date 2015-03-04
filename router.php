<?php

interface Routable
{

    public function route($s_command);
}

function lookup($s_router)
{
    if (! file_exists(NIV . 'routes.php')) {
        return null;
    }
    require (NIV . 'routes.php');
    if (! array_key_exists($s_router, $a_routes)) {
        return null;
    }
    $a_data = $a_routes[$s_router];
    
    if (array_key_exists('query', $a_data)) {
        populateQuery($a_data['query']);
    }
    
    return array(
        'page' => $a_data['router'],
        'command' => $a_data['command']
    );
}

function translate($s_router)
{
    $a_router = explode('/', $s_router);
    
    $s_command = end($a_router);
    $i_end = (strlen($s_router) - strlen('/' . $s_command));
    $s_page = substr($s_router, 0, $i_end);
    
    if (array_key_exists('query', $_GET)) {
        populateQuery($_GET['query']);
        unset($_GET['query']);
    }
    
    $s_page = str_replace(array(
        'http',
        'https',
        'ftp'
    ), array(
        '',
        '',
        ''
    ), $s_page);
    while (strpos($s_page, '../') !== false) {
        $s_page = str_replace('../', '', $s_page);
    }
    
    return array(
        'page' => $s_page,
        'command' => $s_command
    );
}

function populateQuery($s_query)
{
    $a_query = explode('/', $s_query);
    foreach ($a_query as $s_query) {
        $a_parts = explode('=', $s_query);
        $_GET[$a_parts[0]] = $a_parts[1];
    }
}

define('NIV', './');

require (NIV . 'core/exceptions/CoreException.inc.php');

$s_router = $_GET['router'];
while (substr($s_router, - 1) == '/') {
    $s_router = substr($s_router, 0, - 1);
}

$a_data = lookup($s_router);
if (is_null($a_data)) {
    $a_data = translate($s_router);
}

$s_page = $a_data['page'];
$s_command = $a_data['command'];
unset($_GET['router']);

if (! file_exists($s_page . '.php')) {
    @session_start();
    $_SESSION['error'] = 'HTTP 404 : can not find page ' . $s_page;
    include ('errors/404.php');
    exit();
}

$_GET['command'] = $s_command;
$_SERVER['SCRIPT_NAME'] = $s_page . '.php';

require ($s_page . '.php');

$a_class = explode('/', $s_page);
$s_className = UCfirst(end($a_class));

$s_namespace = '\\' . str_replace('/', '\\', $s_page);
$s_namespace = str_replace('\\' . end($a_class), '\\' . $s_className, $s_namespace);

if (! class_exists($s_className)) {
    if (class_exists($s_namespace)) {
        $s_className = $s_namespace;
    } else {
        @session_start();
        $_SESSION['error'] = 'HTTP 500 : class ' . $s_className . ' not found.';
        include ('errors/404.php');
        exit();
    }
}

try {
    $obj_class = new $s_className();
    
    if (! is_subclass_of($obj_class, 'Routable')) {
        $_SESSION['error'] = 'HTTP 500 : class ' . $s_className . ' is not routable';
        include ('errors/404.php');
        exit();
    }
    
    if (! method_exists($obj_class, $s_command)) {
        $_SESSION['error'] = 'HTTP 500 : missing method ' . $s_command;
        include (NIV . 'errors/404.php');
        exit();
    }
    
    $obj_class->route($s_command);
} catch (TemplateException $e) {
    $_SESSION['error'] = 'HTTP 500 : missing method ' . $s_command . ' on page ' . $s_page . '.';
    include (NIV . 'errors/404.php');
    exit();
} catch (CoreException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo ('<!DOCTYPE html>
	<html>
	<head>
		<title>500 Internal Server Error</title>
        <style>
        body {
          background-color:black;
          color:#23c44d;
          margin:5%;
          font-family:Lucida Console, monospace;
        }
        </style>
	</head>
	<body>
	<section id="container">
  		<h1>500 Internal Server Error</h1>
  	
  		<h3>Whoops something went wrong</h3>
  	
  		<h5>Whoops? What whoops?<br/> Computer deactivate the [fill in what you want]</h5>
   
        <h3>System failed to start</h3>
  	
	    <p>' . nl2br($e->__toString()) . '</p>
	</section>
	</body>
	</html>
  	');
    exit();
} catch (Exception $e) {
    @ob_clean();
    
    $_SESSION['error'] = $e->getMessage() . '</p><p>' . nl2br($e->getTraceAsString()) . '</p>';
    $_SESSION['errorObject'] = $e;
    
    \Core\Memory::models('Config')->setPage('errors/500', 'index', 'default');
    
    include (NIV . 'errors/500.php');
    exit();
}
?>
