<?php
/**
 * PM controller model.    Contains the PM models
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   16/06/12
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
class Model_PM extends Model {
	/**
	 * PHP5 constructor
	 */
	public function __construct() {
		parent::__construct();

		require_once(NIV . 'include/models/data/Data_PM.inc.php');
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		$this->a_messages = null;

		parent::__destruct();
	}

	/**
	 * Sends a message from system
	 *
	 * @param	Data_User	$obj_receiver	The receiver
	 * @param	String  $s_title    The title of the message
	 * @param	String  $s_message  The content of the message
	 * @return int 	The new message ID 
	 */
	public function sendSystemMessage($obj_receiver,$s_title, $s_message){
		Memory::type('string', $s_title);
		Memory::type('string', $s_message);

		$i_receiver	= $obj_receiver->getID();

		$obj_message = new Data_PM();
		$obj_message->setSender(0);  // system as sender
		$obj_message->setReceiver($i_receiver);
		$obj_message->setTitle($s_title);
		$obj_message->setMessage($s_message);
		$obj_message->save();

		Memory::services('Mailer')->PM($obj_receiver);
		
		return $obj_message->getID();
	}

	/**
	 * Sends a message
	 *
	 * @param	Data_User	$obj_receiver	The receiver
	 * @param	String  $s_title    The title of the message
	 * @param	String  $s_message  The content of the message
	 * @param int	$i_sender	The sender ID, default current user
	 * @return int The new message ID	
	 */
	public function sendMessage($obj_receiver,$s_title, $s_message,$i_sender = -1) {
		Memory::type('string', $s_title);
		Memory::type('string', $s_message);
		
		if( $i_sender == -1 )		$i_sender	= USERID;

		$i_receiver	= $obj_receiver->getID();

		$obj_message = new Data_PM();
		$obj_message->setSender($i_sender);
		$obj_message->setReceiver($i_receiver);
		$obj_message->setTitle($s_title);
		$obj_message->setMessage($s_message);
		$obj_message->save();

		if( $i_receiver == USERID )
		$this->a_messages[$obj_message->getID()] = $obj_message;
		else
		Memory::services('Mailer')->PM($obj_receiver);
		
		return $obj_message->getID();
	}

	/**
	 * Gets all the messages send to the logged in user
	 *
	 * @param int	$i_receiver	The receiver ID, default current user
	 * @return  array   The messages
	 */
	public function getMessages($i_receiver = -1) {
		if( $i_receiver == -1 )	$i_receiver	= USERID;

		/* Get messages send to the logged in user */
		$this->a_messages = array();
		$this->service_QueryBuilder->select('pm','*')->order('send','DESC')->getWhere()->addAnd('toUserid','i',$i_receiver);
		$service_Database = $this->service_QueryBuilder->getResult();

		$a_messages	= array();
		if( $service_Database->num_rows() != 0) {
			$a_preMessages = $service_Database->fetch_assoc();
			foreach ($a_preMessages AS $a_message) {
				$obj_message = new Data_PM();
				$obj_message->setData($a_message);
				$a_messages[$a_message['id']] = $obj_message;
			}
		}

		return $a_messages;
	}

	/**
	 * Gets the message with the given ID
	 *
	 * @param    int $i_id   The ID of the message
	 * @return   Data_PM   The message
	 * @throws   DBException if the message does not exists
	 */
	public function getMessage($i_id) {
		Memory::type('int', $i_id);

		$obj_message	= new Data_PM();
		$obj_message->loadData($i_id);
		$this->a_messages[$i_id] = $obj_message;

		return $this->a_messages[$i_id];
	}

	/**
	 * Deletes the message with the given ID
	 *
	 * @param   int $i_id The ID of the message
	 * @throws  DBException if the message does not exists
	 */
	public function deleteMessage($i_id) {
		Memory::type('int', $i_id);

		$obj_message	= $this->getMessage($i_id);
		$obj_message->deleteMessage();
	}
}
?>
