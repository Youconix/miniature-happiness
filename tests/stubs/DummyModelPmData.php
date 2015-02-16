<?php
if (! class_exists('\core\models\data\Data_PM')) {
    require (NIV . 'include/models/data/Data_PM.inc.php');
}

class DummyModelPmData extends \core\models\data\Data_PM
{

    /**
     * Loads the PM
     *
     * @param int $i_id
     *            message ID
     * @throws DBException the message does not exist
     */
    public function loadData($i_id)
    {
        throw new \DBException("Requesting unknown message with id " . $i_id);
    }

    /**
     * Returns the message ID
     *
     * @return int The message ID
     */
    public function getID()
    {
        return $this->i_id;
    }

    /**
     * Returns the sender
     *
     * @return Data_User The sender
     */
    public function getSender()
    {
        return $this->obj_sender;
    }

    /**
     * Sets the sender
     *
     * @param int $i_sender
     *            The ID from the sender
     * @throws DBException If the userid is invalid
     */
    public function setSender($i_sender)
    {
        \core\Memory::type('int', $i_sender);
        
        $this->obj_sender = $this->model_User->get($i_sender);
    }

    /**
     * Sets the message as read
     */
    public function setRead()
    {
        if ($this->i_unread == 1) {
            $this->i_unread = 0;
        }
    }

    /**
     * Deletes the message
     */
    public function deleteMessage()
    {}

    /**
     * Saves the new message
     */
    public function save()
    {
        if (is_null($this->i_id)) {
            $this->i_id = rand();
        }
    }
}
?>
