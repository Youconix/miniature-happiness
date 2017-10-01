<?php
use \youconix\core\templating\BaseController as BaseController;
use \youconix\core\helpers\IndexInstall as IndexInstall;
use \includes\BaseLogicClass AS Layout;

/**
 * General landing page.
 *
 * @author : Rachelle Scheijen
 * @copyright Youconix
 * @version 1.0
 * @since 1.0
 */
class Index extends BaseController
{

    /**
     *
     * @var \Language
     */
    protected $language;

    /**
     *
     * @var \core\helpers\IndexInstall
     */
    protected $indexInstall;

    /**
     * Constructor
     *
     * @param \Request $request
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \includes\BaseLogicClass $layout
     * @param \core\helpers\IndexInstall $index            
     */
    public function __construct(\Request $request, \Language $language, \Output $template,Layout $layout, IndexInstall $index)
    {
        parent::__construct($request,$layout,$template);
        
        $this->language = $language;
        $this->indexInstall = $index;
    }

    /**
     * Sets the index content
     */
    public function view()
    {
	$template = $this->createView('index/view',[
	    'title' => ''
	]);
        $this->indexInstall->generate($template);
	
	return $template;
    }
}