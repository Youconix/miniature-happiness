<?php
/**
 * Error-handler for reporting en registrating runtime-errors
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			12/01/2006
 * @changed   		25/09/2010
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
class Service_ErrorHandler extends Service {
	private $service_Logs;

	/**
	 * Destructor
	 */
	public function __destruct(){
		$this->service_Logs         = null;
	}

	/**
	 * Reports the generated error
	 *
	 * @param   Exception   $exception   The exception
	 * @see errorAsString($s_exception)
	 */
	public function error($exception){
		Memory::type('object',$exception);

		$s_exception	= $exception->getMessage().'
'.		$exception->getTraceAsString();
		
		$this->errorAsString($s_exception);
	}

	/**
	 * Reports the generated error
	 *
	 * @param   string   $s_exception   The exception message
	 * @see error($exception)
	 */
	public function errorAsString($s_exception){
		if( is_null($this->service_Logs) ){
			$this->service_Logs         = Memory::services('Logs');
		}

		$s_exception .= "\n\n";		
		$this->service_Logs->errorLog($s_exception);
	}
}
?>
