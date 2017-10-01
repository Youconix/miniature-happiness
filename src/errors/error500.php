<?php

namespace errors;

use \youconix\core\templating\BaseController as BaseController;
use \includes\BaseLogicClass AS Layout;
use \youconix\core\services\Headers AS Headers;

/**
 * Error 500 class
 *
 * @copyright   Youconix
 * @author :	Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
class Error500 extends BaseController {

  /**
   *
   * @var \Language
   */
  private $language;

  /**
   *
   * @var \youconix\core\services\Headers
   */
  private $headers;
  
  /**
   *
   * @var \Logger
   */
  private $logger;

  /**
   * Constructor
   *
   * @param \Request $request
   * @param \Language $language            
   * @param \Output $template
   * @param \youconix\core\services\Headers Headers
   */
  public function __construct(\Request $request, \Language $language, \Output $template, Layout $layout, Headers $headers,\Logger $logger) {
    parent::__construct($request, $layout, $template);

    $this->language = $language;
    $this->headers = $headers;
    $this->logger = $logger;
  }

  /**
   * Displays the error
   */
  public function index(\Exception $exception = null,$s_errorMessage = null) {
    $this->headers->http500();

    $a_data = [
	'title' => t('errors/error500/serverError'),
	'notice' => t('errors/error500/systemError'),
	'debug_notice'=>''
    ];

   if (!is_null($exception) ){
      reportException($exception, false);
    
      if( defined('DEBUG') ){
	$s_trace = str_replace(['<br>','<br/>','<br />'],["\n","\n","\n"],$exception->getTraceAsString());
	$a_data['debug_notice'] = $exception->getMessage() . "\n" . $s_trace;
      }
    }
    if( !is_null($s_errorMessage) ){
      $this->logger->critical($s_errorMessage);
      
      if( defined('DEBUG') ){
	$a_data['debug_notice'] = $s_errorMessage;
      }
    }

    $template = null;
    try {
      $template = $this->createView('errors/error500/index', $a_data);
      
      $template->append('head','<style type="text/css">
	body {
          background-color:black;
          color:#23c44d;
          font-family:Lucida Console, monospace;
          background: linear-gradient(#111, #111) repeat scroll 0 0 rgba(0, 0, 0, 0);
        }
        #content { margin:5%; }
	</style>');
    } catch (\Exception $e) {}

    return $template;
  }

}
