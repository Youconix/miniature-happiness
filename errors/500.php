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
 * Error 500 class
 *
 * @copyright   Youconix
 * @author :	Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
if (! defined('NIV')) {
    define('NIV','../');
    interface Routable
    {
    
        public function route($s_command);
    }
}
if (! class_exists('\includes\BaseLogicClass')) {
    require (NIV . 'includes/BaseLogicClass.php');
}

class Error500 extends \includes\BaseLogicClass
{

    /**
     * Starts the class Error500
     */
    public function __construct()
    {
        $this->init();
        
        \Loader::inject('\core\services\Template')->setCss('body {
          background-color:black;
          color:#23c44d;
          font-family:Lucida Console, monospace;
          background: linear-gradient(#111, #111) repeat scroll 0 0 rgba(0, 0, 0, 0);
        }
        #content { margin:5%; }');
        
        $this->displayError();
        
        $this->showLayout();
    }

    private function displayError()
    {
        header("HTTP/1.1 500 Internal Server Error");
        
        $service_Language = \Loader::inject('\core\services\Language');
        
        $this->service_Template->set('title', $service_Language->get('language/errors/error500/serverError'));
        
        $this->service_Template->set('notice', $service_Language->get('language/errors/error500/systemError'));
        
        if (isset($_SESSION['error'])) {
            $service_Logs = \Loader::Inject('\core\services\Logs');
            
            if (isset($_SESSION['errorObject'])) {
                $service_Logs->exception($_SESSION['errorObject']);
            } else {
                $service_Logs->errorLog($_SESSION['error']);
            }
            
            if (defined('DEBUG')) {
                $this->service_Template->set('debug_notice', $_SESSION['error']);
            }
        }
    }
}

$obj_Error = new Error500();
unset($obj_Error);