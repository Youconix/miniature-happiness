<?php
namespace core\classes;

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
 * Site header
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class HeaderAdmin extends \core\classes\Header
{

    protected $service_Template;

    protected $service_Language;

    protected $model_User;

    protected $s_template;

    /**
     * Starts the class header
     */
    public function __construct(\core\services\Template $service_Template, \core\services\Language $service_Language, \core\models\User $model_User, \core\models\Config $model_Config)
    {
        parent::__construct($service_Template, $service_Language, $model_User, $model_Config);
        
        $this->createHeader();
    }

    /**
     * Generates the header
     */
    public function createHeader()
    {
        $obj_User = $this->model_User->get();
        
        $this->service_Template->set('logout', $this->service_Language->get('system/admin/menu/logout'));
        $this->service_Template->set('close', $this->service_Language->get('system/admin/menu/close'));
        $this->service_Template->set('adminMenuLink', $this->service_Language->get('system/admin/menu/adminMenuLink'));
        $this->service_Template->set('loginHeader', $this->service_Language->insertPath('system/admin/menu/loginHeader', 'name', $obj_User->getUsername()));
        
        $this->displayLanguageFlags();
    }
}