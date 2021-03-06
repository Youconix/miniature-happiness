<?php
/**
 * Observer interface (observer pattern)
 * 
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   09/01/13
 */
interface Observer {
	public function update($observable,$data);
}

/**
 * Observable parent class (observer pattern)
 *
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   09/01/13
 */
abstract class Observable {
 	private $a_observers	= array();
	private $bo_changed	= false;

	/**
	 * Adds the given observer
	 *
	 * @param Observer $observer	The observer
	 * @throws Exception	if $observer does niet implement Observer
	 */
	public function addObserver($observer){
		if( !($observer instanceof Observer) )
			throw new Exception("Can only add observers");
		
		foreach($this->a_observers AS $item){
			if( $item == $observer)
				return;
		}
		
		$this->a_observers[] =	$observer;
	}

	/**
	 * Deletes the given observer
	 * 
	 * @param Observer $observer	The observer
	 */
	public function deleteObserver($observer){
		$i_number	= count($this->a_observers);
		
		for($i=0; $i<$i_number; $i++){
			if( $this->a_observers[$i] == $observer){
				$this->a_observers[$i] = null;
				break;
			}
		}
	}

	/**
	 * Sets the observable as changed
	 */
	public function setChanged(){
		$this->bo_changed	= true;
	}

	/**
	 * Notifies the observers
	 * 
	 * @param string $data	The payload to send
	 */
	public function notifyObservers($data = null){
		if( !$this->bo_changed )	return;

		foreach($this->a_observers AS $observer){
			if( is_null($observer) )	continue;

			$observer->update($this,$data);
		}
	}
}
?>
