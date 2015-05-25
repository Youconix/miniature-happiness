<?php
namespace admin;

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
 * Admin settings configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */

include (NIV . 'core/AdminLogicClass.php');

abstract class Settings extends \core\AdminLogicClass
{

    /**
     *
     * @var \core\services\Settings
     */
    protected $service_Settings;

    /**
     *
     * @var \core\services\FileHandler
     */
    protected $service_FileHandler;

    /**
     *
     * @var \core\services\Builder
     */
    protected $service_Builder;
    
    /**
     * @var \core\services\Headers
     */
    protected $service_Headers;

    /**
     * @param \core\Input $Input    The input parser
     * @param \core\models\Config $model_Config
     * @param \core\services\Language $service_Language
     * @param \core\services\Template $service_Template
     */
    public function __construct(\core\Input $Input,\core\models\Config $model_Config,
        \core\services\Language $service_Language,\core\services\Template $service_Template)
    {
        parent::__construct($Input, $model_Config, $service_Language, $service_Template);
        
        $this->menu();
    }
    
    /**
     * Calls the functions
     */
    abstract protected function menu();

    /**
     * Inits the class Settings
     */
    protected function init()
    {
        parent::init();
        
        $this->service_Settings = \Loader::Inject('\core\services\Settings');
        $this->service_FileHandler = \Loader::Inject('\core\services\FileHandler');
        $this->service_Builder = \Loader::Inject('\core\services\QueryBuilder')->createBuilder();
        $this->service_Headers = \Loader::Inject('\core\services\Headers');
    }

    /**
     * Returns the value
     *
     * @param string $s_key
     *            The key
     * @param string $default
     *            The default value if the key does not exist
     * @return string The value
     */
    protected function getValue($s_key, $default = '')
    {
        if (! $this->service_Settings->exists($s_key)) {
            return $default;
        }
        
        $s_value = $this->service_Settings->get($s_key);
        if (empty($s_value) && ! empty($default)) {
            return $default;
        }
        
        return $s_value;
    }

    /**
     * Sets the value
     *
     * @param string $s_key
     *            The key
     * @param string $s_value
     *            The value
     */
    protected function setValue($s_key, $s_value)
    {
        if (! $this->service_Settings->exists($s_key)) {
            $this->service_Settings->add($s_key, $s_value);
        } else {
            $this->service_Settings->set($s_key, $s_value);
        }
    }
}