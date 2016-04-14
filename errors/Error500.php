<?php
namespace errors;

/**
 * Error 500 class
 *
 * @copyright   Youconix
 * @author :	Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
$_SERVER['REQUEST_URI'] = 'errors/Error500.php';
$config = \Loader::inject('\Config');
$config->detectTemplateDir();

class Error500 extends \core\templating\BaseController
{
    /**
     * 
     * @var \Output
     */
    protected $template;
    
    /**
     * 
     * @var \Language
     */
    protected $language;
    
    /**
     * Starts the class Error404
     * 
     * @param \Request $request
     * @param \Language $language
     * @param \Output $template
     */
    public function __construct(\Request $request,\Language $language,\Output $template)
    {
        $this->template = $template;
        $this->language = $language;
        
        parent::__construct($request);
        
        $this->template->setCss('body {
          background-color:black;
          color:#23c44d;
          font-family:Lucida Console, monospace;
          background: linear-gradient(#111, #111) repeat scroll 0 0 rgba(0, 0, 0, 0);
        }
        #content { margin:5%; }');
    }

    protected function index()
    {
        $this->headers->http500();
        $this->headers->printHeaders();
        
        
        $this->template->set('title', t('errors/error500/serverError'));
        
        $this->template->set('notice', t('errors/error500/systemError'));
        
        if ( $this->session->exists('error') ) {
            if ( $this->session->exists('errorObject') && ( $this->session->get('errorObject') instanceof \Exception) ) {
            	reportException($_SESSION['errorObject'],false);
            } else {
                $this->logger->critical($_SESSION['error']);
            }
            
            if (defined('DEBUG')) {
                $this->template->set('debug_notice', $_SESSION['error']);
            }
            
            $this->session->delete('error');
        }
    }
}