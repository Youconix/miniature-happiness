<?php
namespace admin\modules\general;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Admin cache removal page
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class Cache extends \core\AdminLogicClass
{

    /**
     *
     * @var \Cache
     */
    private $cache;

    /**
     * Starts the class Cache
     *
     * @param \Input $Input    The input parser         
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template
     * @param \Logger $logs     
     * @param \Cache $cache
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template,
        \Logger $logs, \Cache $cache)
    {
        parent::__construct($Input, $config, $language, $template,$logs);
        
        $this->cache = $cache;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            switch ($s_command) {
                case 'language':
                    $this->cache->cleanLanguageCache();
                    break;
                
                case 'site':
                    $this->cache->clearSiteCache();
                    break;
            }
        }
    }
}