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

    protected $init_post = array();

    protected $init_get = array();

    protected $init_request = array();

    /**
     * @var \Input
     */
    protected $post;

    /**
     * @var \Input
     */
    protected $get;

    /**
     * @var \Input
     */
    protected $request;

    /**
     * Base class constructor
     *
     * @param \Input $input    The input parser           
     */
    public function __construct(\Input $input)
    {
        $this->prepareInput($input);
        
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
     * Prepares the inputs
     * 
     * @param \core\Input $input    The input parser
     */
    protected function prepareInput(\core\Input $input){
        $this->post = clone $input;
        $this->get = clone $input;
        $this->request = clone $input;
    }

    /**
     * Inits the class BaseClass
     */
    protected function init()
    {   
        /* Secure input */
        $this->get->parse('GET', $this->init_get);
        $this->post->parse('POST', $this->init_post);
        $this->request->parse('REQUEST', $this->init_request);
    }
}