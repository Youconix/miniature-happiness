<?php
namespace errors;

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
    require (NIV . 'includes/BaseLogicClass.php');
}
define('TEMPLATE', 'errors/404/index');

class Error404 extends \includes\BaseLogicClass
{
    /**
     * @var \core\services\Headers
     */
    protected $service_Headers;
    
    /**
     * @var \core\services\Logs
     */
    protected $service_Logs;

    /**
     * Starts the class Error404
     * 
     * @param \core\services\Security $service_Security
     * @param \core\models\Config $model_Config
     * @param \core\services\Language $service_Language
     * @param \core\services\Template $service_Template
     * @param \core\classes\Header $header
     * @param \core\classes\Menu $menu
     * @param \core\services\Headers $service_Headers
     * @param \core\services\Logs $service_Logs
     */
    public function __construct(\core\services\Security $service_Security,\core\models\Config $model_Config,
        \core\services\Language $service_Language,\core\services\Template $service_Template,\core\classes\Header $header,\core\classes\Menu $menu,\core\classes\Footer $footer,
        \core\services\Headers $service_Headers,\core\services\Logs $service_Logs)
    {
        parent::__construct($service_Security,$model_Config,$service_Language,$service_Template,$header,$menu,$footer);
        
        $this->service_Headers = $service_Headers;
        $this->service_Logs = $service_Logs;
        
        $this->showLayout();
        
        $this->displayError();
    }

    /**
     * Displays the error
     */
    private function displayError()
    {
        $this->service_Headers->http404();
        $this->service_Headers->printHeaders();
        
        $this->service_Template->set('title', t('errors/error404/notFound'));
        
        $this->service_Template->set('notice', t('errors/error404/pageMissing'));
        
        if (isset($_SESSION['error'])) {
            $this->service_Logs->errorLog($_SESSION['error']);
            if (defined('DEBUG')) {
                
                $this->service_Template->set('debug_notice', $_SESSION['error']);
            }
        }
    }
}

\Loader::inject('\errors\Error404');