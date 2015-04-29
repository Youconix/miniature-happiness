<?php
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
 * General landing page.
 *
 * @author:		Rachelle Scheijen
 * @copyright	Youconix
 * @version	1.0
 * @since		1.0
 */
use \core\Memory;
if (! defined('NIV')) {
    define('NIV', './');
}

include (NIV . 'core/BaseLogicClass.php');

class Index extends \core\BaseLogicClass
{

    /**
     * Sets the index content
     */
    protected function view()
    {
        $this->service_Template->set('content',\Loader::inject('\core\helpers\IndexInstall'));
    }
}