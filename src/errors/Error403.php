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
 * Error 403 class
 *
 * @copyright   Youconix
 * @author :	Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
class Error403 extends \core\templating\BaseController
{

    /**
     *
     * @var \Language
     */
    protected $language;

    /**
     *
     * @var \Output
     */
    protected $template;

    /**
     * Starts the class Error504
     */
    public function __construct(\Request $request, \Output $template, \Language $language)
    {
        $this->language = $language;
        $this->template = $template;
        
        parent::__construct($request);
    }

    protected function index()
    {
        header("HTTP/1.1 403 Forbidden");
        
        
        $this->template->set('title', t('errors/error403/accessDenied'));
        
        $this->template->set('notice', t('errors/error403/noRights'));
    }
}