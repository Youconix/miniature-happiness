<?php
namespace core\services;

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
 * Error-handler for reporting en registrating runtime-errors
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 * @deprecated
 *
 * @see \core\services\Logs
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
        trigger_error("This class has been deprecated.",E_USER_DEPRECATED);
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
        $this->service_Logs->exception($exception);
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
        $this->service_Logs->errorLog($s_exception);
    }
}