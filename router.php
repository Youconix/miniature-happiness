<?php

class Router
{
    private $obj_class = null;
    private $s_gui = null;
    
    public function __construct()
    {
        $this->doRouting();
    }
    
    public function __destruct(){
        $this->s_gui = null;
        $this->obj_class = null;
    }

    private function populateGET()
    {
        $_SERVER['REQUEST_URI'] = str_replace('?&', '?', $_SERVER['REQUEST_URI']);
        
        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            $a_query = explode('?', $_SERVER['REQUEST_URI']);
            
            if (strpos($a_query[1], '&') !== false) {
                $a_query = explode('&', $a_query[1]);
            } else {
                $a_query = array(
                    $a_query[1]
                );
            }
            
            foreach ($a_query as $part) {
                $part = explode('=', $part);
                $_GET[$part[0]] = $part[1];
            }
        }
    }

    private function lookup($s_router)
    {
        if (! file_exists(NIV . 'routes.php')) {
            return null;
        }
        require (NIV . 'routes.php');
        
        $a_routeNames = array_keys($a_routes);
        
        $i_length = 0;
        foreach ($a_routeNames as $s_name) {
            $i_length = strlen($s_name);
            if (substr($s_router, 0, $i_length) == $s_name) {
                $a_item = $a_routes[$s_name];
                
                if (! preg_match($a_item['regex'], $s_router, $a_matches)) {
                    continue;
                }
                
                for ($i = 1; $i < count($a_matches); $i ++) {
                    $_GET[$a_item['fields'][($i - 1)]] = $a_matches[$i];
                }
                
                return array(
                    'page' => $a_item['page'],
                    'command' => $a_item['command']
                );
            }
        }
    }

    private function translate($s_router)
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

    private function populateQuery($s_query)
    {
        $a_query = explode('/', $s_query);
        foreach ($a_query as $s_query) {
            $a_parts = explode('=', $s_query);
            $_GET[$a_parts[0]] = $a_parts[1];
        }
    }

    private function doRouting()
    {
        define('NIV', './');
        
        $s_router = $_GET['router'];
        while (substr($s_router, - 1) == '/') {
            $s_router = substr($s_router, 0, - 1);
        }
        
        $a_data = $this->lookup($s_router);
        if (is_null($a_data)) {
            $a_data = $this->translate($s_router);
        }
        
        $s_page = $a_data['page'];
        $s_command = $a_data['command'];
        unset($_GET['router']);
        
        $this->switchPage($s_page, $s_command);        
        $this->populateGET();
        
        require (NIV . 'vendor/youconix/core/bootstrap.php');
        
        $this->loadPage();        
        
        if( !is_null($this->s_gui) ){
            $gui = \Loader::inject($this->s_gui);
            
            if( is_null($gui) ){
                $_SESSION['error'] = 'Call to unknown gui '.$gui.' for namespace '.$s_className.' on view '.$s_command.'.';
                $this->error500();
                exit();
            }
        
            unset($gui);
        }
    }
    
    private function loadPage(){
        if (! file_exists($_SERVER['SCRIPT_NAME'])) {
            /* Page not found */
            $_SESSION['error'] = 'HTTP 404 : can not find page ' . $_SERVER['SCRIPT_NAME'];
            $this->error404();
            return;
        }
        
        require ($_SERVER['SCRIPT_NAME']);
        if (defined('LAYOUT')) {
            \Loader::Inject('\Config')->setLayout(LAYOUT);
        }
        
        $s_page = str_replace('.php', '', $_SERVER['SCRIPT_NAME']);
        
        $a_class = explode('/', $s_page);
        $s_className = UCfirst(end($a_class));
        
        $s_namespace = '\\' . str_replace('/', '\\', $s_page);
        $s_namespace = str_replace('\\' . end($a_class), '\\' . $s_className, $s_namespace);
        
        @session_start();
        
        if (!class_exists($s_namespace)) {
            /* Namespace not found */
            $_SESSION['error'] = 'HTTP 500 : class ' . $s_className . ' not found.';
            $this->error404();
            return;
        }
        
        $s_className = $s_namespace;
        $s_command = $_GET['command']; // make sure we have the right one after privileges check
        
        try {
            $this->loadClass($s_namespace,$s_command);
        } catch (\BadMethodCallException $e) {
            $_SESSION['errorObject'] = $e;
            $this->error500();
        } catch (\TemplateException $e) {
            $_SESSION['error'] = 'HTTP 500 : ' . $e->getMessage();
            $this->error404();
        } catch (\CoreException $e) {
            $this->coreException($e);
        } catch (\Exception $e) {
            @ob_clean();
        
            $_SESSION['error'] = $e->getMessage() . '</p><p>' . nl2br($e->getTraceAsString()) . '</p>';
            $_SESSION['errorObject'] = $e;
            
            $this->error500();
        }
    }
    
    private function error404(){
        @session_start();
        \Loader::Inject('\Config')->setPage('errors/Error404', 'index', 'default');
        
        $this->switchPage('errors/Error404', 'index');
        $this->loadClass('\errors\Error404','index');
    }
    
    private function error500(){
        \Loader::Inject('\Config')->setPage('errors/Error500', 'index', 'default');
        
        $this->loadClass('\errors\Error500','index');
    }
    
    private function getGui($s_namespace,$s_view){
        $builder = \Loader::Inject('Builder');
        $config = \Loader::Inject('\Config');
        
        if( $config->isAjax() ){
            return null;
        }
        
        while( substr($s_namespace, 0,1) == '\\' ){
            $s_namespace = substr($s_namespace, 1);
        }
        
        try {
            $builder->select('pages_gui','gui,view')->getWhere()->bindString('namespace',$s_namespace);
            $database = $builder->getResult();
            
            $s_all = null;
            if( $database->num_rows() > 0 ){
                $a_data = $database->fetch_assoc();
                foreach($a_data AS $a_item){
                    if( $a_item['view'] == $s_view ){
                        return $a_item['gui'];
                    }
                    if( $a_item['view'] == 'all' ){
                        $s_all = $a_item['gui'];
                    }
                }
                return $s_all;
            }
            
            return '\includes\BaseLogicClass';
        }
        catch(\Exception $e){
            return null;
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
    
    private function switchPage($s_page,$s_command){
        $_GET['command'] = $s_command;
        $_SERVER['SCRIPT_NAME'] = $s_page . '.php';
    }
    
    private function loadClass($s_namespace,$s_command){
        $obj_class = \Loader::inject($s_namespace);
        
        if (! is_subclass_of($obj_class, 'Routable')) {
            throw new \BadMethodCallException('HTTP 500 : class ' . $s_className . ' is not routable');
        }
        
        $obj_class->route($s_command);
        $this->obj_class = $obj_class;
        $this->s_gui = $this->getGui($s_namespace,$s_command);
    }
}

$router = new Router();
?>
