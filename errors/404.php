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
 * Error 404 class
 *
 * @copyright   Youconix
 * @author :	Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
if (! defined('NIV')) {
    define('NIV', '../');
    interface Routable
    {
    
        public function route($s_command);
    }
}
define('TEMPLATE', 'errors/404/index');

use \core\Memory;

if (! class_exists('\includes\BaseLogicClass')) {
    require (NIV . 'includes/BaseLogicClass.php');
}

class Error404 extends \includes\BaseLogicClass
{

    /**
     * Starts the class Error504
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->showLayout();
        
        $this->displayError();
    }

    private function displayError()
    {
        header("HTTP/1.1 404 Not found");
        
        $service_Language = \Loader::inject('\core\services\Language');
        
        $this->service_Template->set('title', $service_Language->get('language/errors/error404/notFound'));
        
        $this->service_Template->set('notice', $service_Language->get('language/errors/error404/pageMissing'));
        
        if (isset($_SESSION['error'])) {
            $service_Logs = \Loader::inject('\core\services\Logs');
            $service_Logs->errorLog($_SESSION['error']);
            
            if (defined('DEBUG')) {
                
                $this->service_Template->set('debug_notice', $_SESSION['error']);
            }
        }
    }
}

$obj_Error = new Error404();
unset($obj_Error);