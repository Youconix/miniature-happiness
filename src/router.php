<?php

class Router
{
    private $config;
    private $routes;
    private $exception;
    
    public function __construct()
    {
      define('NIV','./');
      
      try {
	require (NIV . 'vendor/youconix/core/bootstrap.php');
	require(NIV.'vendor/youconix/core/Routes.php');

        $this->config = \Loader::inject('\Config');
        $this->routes = \Loader::inject('\youconix\core\Routes');	

	$this->doRouting();
      }
      catch(CoreException $ex){
	$this->coreException($ex);
      }
      catch (Exception $ex) {
	print_r($ex->getMessage());
	print_r($ex->getTrace());
	if( !is_null($this->exception) ){ //loop 
	  $this->coreException($ex);
	  return;
	}
	
	$this->exception = $ex;
	try {
	  $_SERVER['PHP_SELF'] = '/router.php/errors/error500/index';
	  
	  $this->doRouting();
	} catch (Exception $ex) {
	  $this->coreException($ex);
	}
      }
      
      if (class_exists('\youconix\core\Memory')) {
	\youconix\core\Memory::endProgram();
      }
    }
    
    public function __destruct(){
        $this->s_gui = null;
        $this->obj_class = null;
    }

    private function doRouting(){
      try {
	$this->routes->clearException();
	if( !is_null($this->exception) ){
	  $this->routes->setException($this->exception);
	}
	$controller = $this->routes->findController($this->config);
	
	$result = $this->routes->getResult();
	if( $result instanceof \Output ){
	  $result->printToScreen();
	}
	
	unset($controller);
	unset($result);
      } 
      catch(Http401Exception $ex){
	$_SERVER['PHP_SELF'] = '/router.php/login/index';
	
	$this->doRouting();
      }
      catch(Http403Exception $ex){
	$_SERVER['PHP_SELF'] = '/router.php/errors/error403/index';
	
	$this->doRouting();
      }
      catch(Http404Exception $ex){
	$_SERVER['PHP_SELF'] = '/router.php/errors/error404/index';
	
	$this->doRouting();
      }
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
