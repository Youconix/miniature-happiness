<?php
use \youconix\core\templating\BaseController as BaseController;
use \youconix\core\helpers\IndexInstall as IndexInstall;

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
     * @var \Output
     */
    protected $template;

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
     * @param \core\classes\Header $header            
     * @param \core\classes\Menu $menu            
     * @param \core\classes\Footer $footer            
     * @param \core\helpers\IndexInstall $index            
     */
    public function __construct(\Request $request, \Language $language, \Output $template, IndexInstall $index)
    {
        parent::__construct($request);
        
        $this->template = $template;
        $this->language = $language;
        $this->indexInstall = $index;
    }

    /**
     * Sets the index content
     */
    public function view()
    {
        $this->template->set('content', $this->indexInstall->generate());
        
        try {
        $builder = \Loader::inject('\DatabaseParser');
        $parser = simplexml_load_file(NIV.DATA_DIR.'database/0_framework.xml');
        print_r($builder->updateTables($parser));
        }
        catch(\Exception $e){
            print_r($e);
        }
    }
}