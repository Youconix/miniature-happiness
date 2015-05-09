<?php
namespace tests\stubs\models\data;

class DummyModelGroupData extends \core\models\data\DataGroup
{

    public function __construct()
    {}

    /**
     * Gets the user access level
     *
     * @param int $i_userid
     *            The user ID
     * @return int The access level defined in /include/services/Session.inc.php
     */
    public function getLevelByGroupID($i_userid)
    {
        return 0;
    }

    /**
     * Gets all the members from the group
     *
     * @return array The members from the group
     */
    public function getMembersByGroup()
    {
        return array();
    }

    /**
     * Saves the new group
     */
    public function save()
    {}

    /**
     * Saves the changed group
     */
    public function persist()
    {}

    /**
     * Deletes the group
     */
    public function deleteGroup()
    {}

    /**
     * Adds a user to the group
     *
     * @param int $i_userid
     *            userid
     * @param int $i_level
     *            access level, default 0 (user)
     */
    public function addUser($i_userid, $i_level = 0)
    {}

    /**
     * Edits the users access rights for this group
     *
     * @param int $i_userid
     *            userid
     * @param int $i_level
     *            access level, default 0 (user)
     */
    public function editUser($i_userid, $i_level = 0)
    {}

    /**
     * Adds all the users to this group if the group is default
     */
    public function addUsersToDefault()
    {}

    /**
     * Deletes the user from the group
     *
     * @param int $i_userid
     *            userid
     */
    public function deleteUser($i_userid)
    {}

    /**
     * Checks if the group is in use
     *
     * @return boolean if the group is in use
     */
    public function inUse()
    {
        return true;
    }
}
?>
