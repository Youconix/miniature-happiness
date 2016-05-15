<?php

class Router
{
    
    public function __construct()
    {
      define('NIV','./');
      
      $this->doRouting();
    }
    
    public function __destruct(){
        $this->s_gui = null;
        $this->obj_class = null;
    }

    private function doRouting(){
      try {
	require (NIV . 'vendor/youconix/core/bootstrap.php');
	
	require(NIV.'vendor/youconix/core/Routes.php');
	require(NIV.'routes.php');

	$controller = \youconix\core\Routes::findController();
		
	/* Check login */
	\Profiler::profileSystem('core/models/Privileges', 'Checking access level');
	\Loader::inject('\youconix\core\models\Privileges')->checkLogin();
	\Profiler::profileSystem('core/models/Privileges', 'Checking access level completed');
	
	unset($controller);
      } 
      catch(InvalidArgumentException $ex){
	$this->error404();
      }
      catch(CoreException $ex){
	$this->coreException($ex);
      }
      catch (Exception $ex) {
	$this->error500();
      }
    }
    
    private function error404(){
        @session_start();
        \Loader::Inject('\Config')->setPage('errors/Error404', 'index', 'default');
        
        $this->switchPage('errors/Error404', 'index');
        $this->loadClass('\errors\Error404','index');
	
	$controller = \Loader::inject('errors/Error404');
	unset($controller);
    }
    
    private function error500(){
        \Loader::Inject('\Config')->setPage('errors/Error500', 'index', 'default');
        
        $controller = \Loader::inject('errors/Error500');
	unset($controller);
    }

    private function coreException($e)
    {
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
    }
}

$router = new Router();
?>
