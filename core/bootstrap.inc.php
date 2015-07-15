<?php
/* Set error catcher */
function exception_handler($exception)
{
    if (defined('DEBUG')) {
        $headers = \Loader::Inject('\Headers');
        $headers->http500();
        $headers->printHeaders();
        echo ('<!DOCTYPE html>
		<html>
		<head>
			<title>500 Internal Server Error</title>
		</head>
		<body>
		<section id="container">
  			<h1>500 Internal Server Error</h1>
  	
  			<h3>Whoops something went wrong</h3>
  	
  			<h5>Whoops? What whoops?<br/> Computer deactivate the [fill in what you want]</h5>
  	
		    <p>' . nl2br($exception->__toString()) . '</p>
		</section>
		</body>
		</html>
  	');
        exit();
    }

    include (WEBSITE_ROOT . 'errors/Error500.php');
    exit();
}

set_exception_handler('exception_handler');

interface Routable
{

    public function route($s_command);
}

/**
 * Start framework
 */
require_once (NIV . 'core/Memory.php');
\core\Memory::startUp();

/* Check login */
\Loader::inject('\core\models\Privileges')->checkLogin();
