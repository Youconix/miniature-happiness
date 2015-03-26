<?php
define('PROCESS', 'true');
define('NIV','../');

Class Combiner {
    /**
     * @var \core\models\Config
     */
    protected $model_Config;
    /**
     * @var \core\services\Headers
     */
    protected $service_Headers;
    /**
     * @var \core\services\File
     */
    protected $service_File;
    
    private $a_types = array('javascript','css');
    private $s_file = "";
    
    public function __construct() {
        $this->init();
        
        $this->parse();
    }
    
    private function init() {
        require_once (NIV . 'core/Memory.php');
        \core\Memory::startUp();
        
        $this->model_Config = \Loader::Inject('\core\models\Config');
        $this->service_Headers = \Loader::Inject('\core\services\Headers');
        $this->service_File = \Loader::Inject('\core\services\File');
        
        if (! isset($_GET['type']) || ! in_array($_GET['type'], $this->a_types) || ! isset($_GET['files']) ) {
            $this->service_Headers->http400();
            $this->service_Headers->printHeaders();
            exit();
        }
    }
    
    private function parse() {
        switch($_GET['type']) {
            case 'javascript':
                $this->parseJavascript();
                $this->service_Headers->setJavascript();
                break;
            case 'css':
                $this->parseCSS();
                $this->service_Headers->setCSS();
                break;
        }
        
        $this->service_Headers->printHeaders();
        echo $this->s_file;
        exit();
    }
    
    private function parseJavascript() {
        $s_hash = sha1($_GET['files']);
        $this->hasCache($s_hash.".js");
        
        $a_files = explode(",", $_GET['files']);
        foreach( $a_files AS $s_file ){
            if( substr($s_file,-3) != '.js' ){
                continue;
            }
            $s_file = $this->cleanFilename($s_file);
        
            if (! $this->service_File->exists(NIV.$s_file)) {
                continue;
            }
        
            $s_content = $this->service_File->readFile(NIV.$s_file);
        
            if(!defined('DEBUG') ){
                $s_content = $this->compressJS($s_content);
            }
        
            $this->s_file .= $s_content."\n";
        }
        
        $this->writeCache($s_hash.".js");
    }
    
    private function parseCSS() {
        $s_hash = sha1($_GET['files']);
        $this->hasCache($s_hash.".css");        
        
        $a_files = explode(",", $_GET['files']);
        foreach( $a_files AS $s_file ){
            if( substr($s_file,-4) != '.css' && substr($s_file, -5) != '.less' ){
                continue;
            }
            $s_file = $this->cleanFilename($s_file);
            
            if (! $this->service_File->exists(NIV.$s_file)) {
                continue;
            }
            
            $s_content = $this->service_File->readFile(NIV.$s_file);
            
            if( substr($s_file,-3) == 'css' && !defined('DEBUG') ){
                $s_content = $this->compressCSS($s_content);
            }
            
            $this->s_file .= $s_content."\n";
        }
        
        $this->writeCache($s_hash.".css");
    }
    
    private function cleanFilename($s_filename){
        while( strpos($s_filename,'../') !== false || strpos($s_filename,'./') !== false ){
            $s_filename = str_replace(array('./','../'),array('',''),$s_filename);
        }
        
        return $s_filename;
    }
    
    private function compressCSS($s_content){
        include(NIV.'/lib/cssmin-v3.0.1.php');
        
        return CssMin::minify( file_get_contents($s_content) );
    }
    
    private function compressJS($s_content){
        include(NIV.'/lib/tedious/src/JShrink/Minifier.php');
        
        return \JShrink\Minifier::minify($s_content, array('flaggedComments' => false) );
    }
    
    private function hasCache($s_filename){
        // TODO
    }
    
    private function writeCache($s_filename) {
       // TODO
    }
}

$combiner = new Combiner();