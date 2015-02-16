<?php
namespace core\services;

/**
 * Error-handler for reporting en registrating runtime-errors
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2014,2015,2016 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 *        @date 12/01/2006
 *        @changed 30/03/2014
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class ErrorHandler extends Service
{

    private $service_Logs;

    /**
     * PHP 5 constructor
     *
     * @param \core\services\Logs $service_Logs
     *            The logging service
     */
    public function __construct(\core\services\Logs $service_Logs)
    {
        $this->service_Logs = $service_Logs;
    }

    /**
     * Reports the generated error
     *
     * @param Exception $exception
     *            The exception
     * @see errorAsString($s_exception)
     */
    public function error($exception)
    {
        \core\Memory::type('object', $exception);
        
        $s_exception = $exception->__toString();
        
        $this->errorAsString($s_exception);
    }

    /**
     * Reports the generated error
     *
     * @param String $s_exception
     *            The exception message
     * @see error($exception)
     */
    public function errorAsString($s_exception)
    {
        $s_exception .= "\n\n";
        $this->service_Logs->errorLog($s_exception);
    }
}

?>
