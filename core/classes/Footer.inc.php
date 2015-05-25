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
 * Site footer
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Footer
{

    /**
     * 
     * @var \core\services\Template
     */
    protected $template;
    
    /**
     * 
     * @var \core\services\Settings
     */
    protected $settings;

    /**
     * Starts the class footer
     * 
     * @param core\services\Template $template
     * @param core\services\Settings  $settings
     */
    public function __construct(\core\services\Template $template,\core\services\Settings $settings)
    {
        $this->template = $template;
        $this->settings = $settings;
    }

    /**
     * Generates the footer
     */
    public function createFooter()
    {
        $this->template->set('version', $this->settings->get('version'));
    }
}