<?php
namespace admin\modules\general;

/**
 * Admin cache removal page
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class Cache extends \admin\AdminController
{

    /**
     *
     * @var \Cache
     */
    private $cache;

    /**
     * Starts the class Cache
     *
     * @param \Request $request
     * @param \Config $config
     * @param \Language $language
     * @param \Output $template
     * @param \Logger $logs
     * @param \Headers $headers
     * @param \Cache $cache
     */
    public function __construct(\Request $request, \Config $config, \Language $language, \Output $template, \Logger $logs, \Headers $headers, \Cache $cache)
    {
        parent::__construct($request, $language, $template, $logs, $headers);
        
        $this->cache = $cache;
    }

    public function language()
    {
        $this->cache->cleanLanguageCache();
    }

    public function site()
    {
        $this->cache->clearSiteCache();
    }
}