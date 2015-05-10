<?php
namespace core;

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
 * Base class for the framework.
 * Use this file as parent
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
abstract class BaseClass
{

    /**
     *
     * @var \core\services\Security
     */
    protected $service_Security;

    protected $init_post = array();

    protected $init_get = array();

    protected $init_request = array();

    protected $post = array();

    protected $get = array();

    protected $request = array();

    /**
     * Base class constructor
     *
     * @param \core\services\Security $service_Security           
     */
    public function __construct(\core\services\Security $service_Security)
    {
        $this->service_Security = $service_Security;
        
        $this->init();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (class_exists('\core\Memory')) {
            Memory::endProgram();
        }
    }

    /**
     * Inits the class BaseClass
     */
    protected function init()
    {   
        /* Secure input */
        $this->get = $this->service_Security->secureInput('GET', $this->init_get);
        $this->post = $this->service_Security->secureInput('POST', $this->init_post);
        $this->request = $this->service_Security->secureInput('REQUEST', $this->init_request);
    }
}