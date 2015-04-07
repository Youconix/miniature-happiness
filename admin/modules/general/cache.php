<?php
namespace admin;

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
define('NIV', '../../../');
include (NIV . 'core/AdminLogicClass.php');

class Cache extends \core\AdminLogicClass
{

    private $service_Cache;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        if (! \core\Memory::models('Config')->isAjax()) {
            exit();
        }
        
        if (isset($this->post['command'])) {
            switch ($this->post['command']) {
                case 'language':
                    $this->service_Cache->cleanLanguageCache();
                    break;
                
                case 'site':
                    $this->service_Cache->clearSiteCache();
                    break;
            }
        }
    }

    /**
     * Inits the class Groups
     */
    protected function init()
    {
        parent::init();
        
        $this->service_Cache = \Loader::Inject('\core\services\Cache');
    }
}

$obj_Cache = new Cache();
unset($obj_Cache);