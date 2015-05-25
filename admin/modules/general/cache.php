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
     * @var \core\services\Cache
     */
    private $service_Cache;

    /**
     * Starts the class Cache
     *
     * @param \core\Input $Input    The input parser         
     * @param \core\models\Config $model_Config            
     * @param \core\services\Language $service_Language            
     * @param \core\services\Template $service_Template            
     */
    public function __construct(\core\Input $Input, \core\models\Config $model_Config, \core\services\Language $service_Language, \core\services\Template $service_Template, \core\services\Cache $service_Cache)
    {
        parent::__construct($Input, $model_Config, $service_Language, $service_Template);
        
        $this->service_Cache = $service_Cache;
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
                    $this->service_Cache->cleanLanguageCache();
                    break;
                
                case 'site':
                    $this->service_Cache->clearSiteCache();
                    break;
            }
        }
    }
}