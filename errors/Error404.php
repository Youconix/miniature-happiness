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

$_SERVER['REQUEST_URI'] = 'errors/Error404.php';
$config = \Loader::inject('\Config');
$config->detectTemplateDir();

class Error404 extends \includes\BaseLogicClass
{
    /**
     * @var \Headers
     */
    protected $headers;
    
    /**
     * @var \Logger
     */
    protected $logs;

    /**
     * Starts the class Error404
     * 
     * @param \Input $input
     * @param \Config $config
     * @param \Language $language
     * @param \Output $template
     * @param \Header $header
     * @param \Menu $menu
     * @param \Footer $footer
     * @param \Headers $headers
     * @param \Logger $logs
     */
    public function __construct(\Input $input,\Config $config,\Language $language,\Output $template,
        \Header $header, \Menu $menu, \Footer $footer,\Headers $headers,\Logger $logs)
    {
        parent::__construct($input,$config,$language,$template,$header,$menu,$footer);
        
        $this->headers = $headers;
        $this->logs = $logs;
        
        $this->showLayout();
        
        $this->displayError();
    }

    /**
     * Displays the error
     */
    private function displayError()
    {
        $this->headers->http404();
        $this->headers->printHeaders();
        
        $this->template->set('title', t('errors/error404/notFound'));
        
        $this->template->set('notice', t('errors/error404/pageMissing'));
        
        if (isset($_SESSION['error'])) {
            $this->logs->critical($_SESSION['error']);
            if (defined('DEBUG')) {
                
                $this->template->set('debug_notice', $_SESSION['error']);
            }
        }
    }
}

\Loader::inject('\errors\Error404');