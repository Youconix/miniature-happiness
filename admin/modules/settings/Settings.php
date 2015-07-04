<?php
namespace admin\modules\settings;

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

abstract class Settings extends \core\AdminLogicClass
{

    /**
     *
     * @var \Settings
     */
    protected $settings;

    /**
     * Constructor
     * 
     * @param \Input $Input
     * @param \Config $config
     * @param \Language $language
     * @param \Output $template
     * @param \Logger $logs
     * @param \Settings $settings
     */
    public function __construct(\Input $Input,\Config $config,\Language $language,\Output $template,\Logger $logs,\Settings $settings){
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->settings = $settings;
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
        if (! $this->settings->exists($s_key)) {
            return $default;
        }
        
        $s_value = $this->settings->get($s_key);
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
        if (! $this->settings->exists($s_key)) {
            $this->settings->add($s_key, $s_value);
        } else {
            $this->settings->set($s_key, $s_value);
        }
    }
}