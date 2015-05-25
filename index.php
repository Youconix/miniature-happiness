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
class Index extends \includes\BaseLogicClass
{

    /**
     *
     * @var \core\helpers\IndexInstall
     */
    protected $indexInstall;

    /**
     * Constructor
     *
     * @param \core\Input $Input    The input parser       
     * @param \core\models\Config $config            
     * @param \core\services\Language $language            
     * @param \core\services\Template $template            
     * @param \core\classes\Header $header            
     * @param \core\classes\Menu $menu    
     * @param \core\classes\Footer $footer
     * @param \core\helpers\IndexInstall $index        
     */
    public function __construct(\core\Input $Input, \core\models\Config $config, \core\services\Language $language, \core\services\Template $template, \core\classes\Header $header, \core\classes\Menu $menu, \core\classes\Footer $footer, \core\helpers\IndexInstall $index)
    {
        parent::__construct($Input, $config, $language, $template, $header, $menu, $footer);
        
        $this->indexInstall = $index;
    }

    /**
     * Sets the index content
     */
    protected function view()
    {
        $this->template->set('content', $this->indexInstall->generate());
    }
}