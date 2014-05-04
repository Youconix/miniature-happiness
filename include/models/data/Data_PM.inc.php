<?php
namespace core\models\data;

/**
 * Personal message data model.  Contains the personal message data
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   04/05/2014
 *
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
class Data_PM extends \core\models\Model {
	private $model_User;
	private $i_id;
	private $obj_sender;
	private $i_receiverID;
	private $s_title;
	private $s_message;
	private $i_sendTime;
	private $i_unread;

	/**
   * PHP5 constructor
   * 
   * @param \core\services\QueryBuilder $service_QueryBuilder The query builder
   * @parma \core\services\Security $service_Security The security service
   * @parma \core\models\User $model_User   The user model
   */
  public function __construct(\core\services\QueryBuilder $service_QueryBuilder,\core\services\Security $service_Security,\core\models\User $model_User){
		parent::__construct($service_QueryBuilder,$service_Security);

		$this->model_User = $model_User;
    
    $this->a_validation = array(
        'i_receiverID'  => array('type'=>'int','required'=>1),
        's_title' => array('type'=>'string','required'=>1),
        's_message' => array('type'=>'string','required'=>1),
        'i_sendTime' => array('type'=>'int','min-value'=>time()),
        'i_unread' => array('type'=>'enum','set'=>array(0,1))
    );
	}
	/**
	 * Loads the PM
	 * 
	 * @param int $i_id		The message ID
	 * @throws DBException	If the message does not exist
	 */
	public function loadData($i_id){
    $this->service_QueryBuilder->select('pm','*')->order('send','DESC')->getWhere()->addAnd('id','i',$i_id);
    $service_Database = $this->service_QueryBuilder->getResult();
    
		if( $service_Database->num_rows() == 0 ){
			throw new \DBException("Requesting unknown message with id " . $i_id);
		}

		$a_message	= $service_Database->fetch_assoc();
		$this->setData($a_message[0]);
	}

	/**
	 * Fills the object
	 *
	 * @param array $a_message  The message data from the database
	 */
	public function setData($a_message) {
		\core\Memory::type('array',$a_message);

		$this->i_id = $a_message['id'];
		$this->obj_sender = $this->model_User->get($a_message['fromUserid']);
		$this->i_receiverID = $a_message['toUserid'];
		$this->s_title = $a_message['title'];
		$this->s_message = $a_message['message'];
		$this->i_sendTime = $a_message['send'];
		$this->i_unread = $a_message['unread'];
	}

	/**
	 * Returns the message ID
	 *
	 * @return int  The message ID
	 */
	public function getID(){
		return $this->i_id;
	}

	/**
	 * Returns the sender
	 *
	 * @return Data_User    The sender
	 */
	public function getSender(){
		return $this->obj_sender;
	}

	/**
	 * Sets the sender
	 *
	 * @param int $i_sender The ID from the sender
	 * @throws  DBException If the userid is invalid
	 */
	public function setSender($i_sender){
		\core\Memory::type('int',$i_sender);

		$this->obj_sender   = $this->model_User->get($i_sender);
	}

	/**
	 * Sets the receiver ID
	 * For new messages only
	 *
	 * @param int $i_receiver The receiver ID
	 */
	public function setReceiver($i_receiver){
		\core\Memory::type('int',$i_receiver);

		$this->i_receiverID = $i_receiver;
	}
	
	/**
	 * Returns the receiver ID
	 * 
	 * @return int	The ID
	 */
	public function getReceiver(){
		return $this->i_receiverID;
	}

	/**
	 * Returns the message title
	 *
	 * @return string The message title
	 */
	public function getTitle(){
		return $this->s_title;
	}

	/**
	 * Sets the message title
	 *
	 * @param string $s_title The message title
	 */
	public function setTitle($s_title){
		\core\Memory::type('string',$s_title);

		$this->s_title  = $s_title;
	}

	/**
	 * Returns the message content
	 *
	 * @return string   The message content
	 */
	public function getMessage(){
		return $this->s_message;
	}

	/**
	 * Sets the message content
	 *
	 * @param string $s_message     The message content
	 */
	public function setMessage($s_message){
		\core\Memory::type('string',$s_message);

		$this->s_message    = $s_message;
	}

	/**
	 * Returns if the message is allready read
	 *
	 * @return boolean  True if the message is unread, otherwise false
	 */
	public function isUnread(){
		return ($this->i_unread == 1);
	}

	/**
	 * Sets the message as read
	 */
	public function setRead(){
		if( $this->i_unread == 1 ){
			$this->i_unread   = 0;
      $this->service_QueryBuilder->update('pm','id','i',$this->i_id)->getResult();
		}
	}

	/**
	 * Returns the send time as a timestamp
	 *
	 * @return int  The send time
	 */
	public function getTime(){
		return $this->i_sendTime;
	}

	/**
	 * Deletes the message
	 */
	public function deleteMessage(){
    $this->service_QueryBuilder->delete('pm')->getWhere()->addAnd('id','i',$this->i_id);
    $this->service_QueryBuilder->getResult();
	}

	/**
	 * Saves the new message
	 */
	public function save(){
    if( !is_null($this->i_id) ){ return;  }
    $this->performValidation();

		$this->i_sendTime   = time();
    $this->service_QueryBuilder->insert('pm',array('toUserid','fromUserid','title','message','send'),array('i','i','s','s','i'),
      array($this->i_receiverID,$this->obj_sender->getID(), $this->s_title,$this->s_message,$this->i_sendTime));
    
    $service_Database = $this->service_QueryBuilder->getResult();

		$this->i_id = (int)$service_Database->getId();
	}
}
?>