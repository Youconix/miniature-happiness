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
 * Error 500 class
 *
 * @copyright   Youconix
 * @author :	Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
$_SERVER['REQUEST_URI'] = 'errors/Error500.php';
$config = \Loader::inject('\Config');
$config->detectTemplateDir();

class Error500 extends \includes\BaseLogicClass
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
    public function __construct(\Input $input,\Config $config,
        \Language $language,\Output $template,\Header $header, \Menu $menu, \Footer $footer,
        \Headers $headers,\Logger $logs)
    {
        parent::__construct($input,$config,$language,$template,$header,$menu,$footer);
        
        $this->headers = $headers;
        $this->logs = $logs;
        
        $this->template->setCss('body {
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
        $this->headers->http500();
        $this->headers->printHeaders();
        
        
        $this->template->set('title', t('errors/error500/serverError'));
        
        $this->template->set('notice', t('errors/error500/systemError'));
        
        if (isset($_SESSION['error'])) {
            if (isset($_SESSION['errorObject'])) {
                $this->logs->exception($_SESSION['errorObject']);
            } else {
                $this->logs->errorLog($_SESSION['error']);
            }
            
            if (defined('DEBUG')) {
                $this->template->set('debug_notice', $_SESSION['error']);
            }
        }
    }
}

\Loader::inject('\errors\Error500');