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
    protected $helper_IndexInstall;

    /**
     * Constructor
     *
     * @param \core\services\Security $service_Security            
     * @param \core\models\Config $model_Config            
     * @param \core\services\Language $service_Language            
     * @param \core\services\Template $service_Template            
     * @param \core\classes\Header $header            
     * @param \core\classes\Menu $menu            
     */
    public function __construct(\core\services\Security $service_Security, \core\models\Config $model_Config, \core\services\Language $service_Language, \core\services\Template $service_Template, \core\classes\Header $header, \core\classes\Menu $menu, \core\classes\Footer $footer, \core\helpers\IndexInstall $helper_Index)
    {
        parent::__construct($service_Security, $model_Config, $service_Language, $service_Template, $header, $menu, $footer);
        
        $this->helper_IndexInstall = $helper_Index;
    }

    /**
     * Sets the index content
     */
    protected function view()
    {
        $this->service_Template->set('content', $this->helper_IndexInstall->generate());
    }
}