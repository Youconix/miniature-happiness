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
 * Error 403 class
 *
 * @copyright   Youconix
 * @author :	Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
define('NIV', '../');
if (! class_exists('\includes\BaseLogicClass')) {
    require (NIV . 'includes/BaseLogicClass.php');
}

class Error403 extends \includes\BaseLogicClass
{

    /**
     * Starts the class Error504
     */
    public function __construct()
    {
        $this->init();
        
        $this->displayError();
        
        $this->showLayout();
    }

    private function displayError()
    {
        header("HTTP/1.1 403 Forbidden");
        
        $service_Language = Memory::services('Language');
        
        $this->service_Template->set('title', $service_Language->get('language/errors/error403/accessDenied'));
        
        $this->service_Template->set('notice', $service_Language->get('language/errors/error403/noRights'));
        
        if (isset($_SESSION['error'])) {
            if (isset($_SESSION['errorObject'])) {
                Memory::services('ErrorHandler')->error($_SESSION['errorObject']);
            } else {
                Memory::services('ErrorHandler')->errorAsString($_SESSION['error']);
            }
            
            if (defined('DEBUG')) {
                $this->service_Template->set('debug_notice', $_SESSION['error']);
            }
        }
    }
}

$obj_Error = new Error403();
unset($obj_Error);