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
 * @copyright Youconix
 * @author : Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
$_SERVER['REQUEST_URI'] = 'errors/Error404.php';
$config = \Loader::inject('\Config');
$config->detectTemplateDir();

class Error404 extends \core\templating\BaseController
{

    /**
     *
     * @var \Language
     */
    private $language;

    /**
     *
     * @var \Output
     */
    private $template;

    /**
     * Starts the class Error404
     *
     * @param \Request $request            
     * @param \Language $language            
     * @param \Output $template            
     */
    public function __construct(\Request $request, \Language $language, \Output $template)
    {
        $this->language = $language;
        $this->template = $template;
        
        parent::__construct($request);
    }

    /**
     * Displays the error
     */
    protected function index()
    {
        $this->headers->http404();
        $this->headers->printHeaders();
        
        $this->template->set('title', t('errors/error404/notFound'));
        
        $this->template->set('notice', t('errors/error404/pageMissing'));
        
        if ( $this->session->exists('error') ) {            
            if (defined('DEBUG')) {
                $this->template->set('debug_notice', $_SESSION['error']);
            }
        
            $this->session->delete('error');
        }
    }
}