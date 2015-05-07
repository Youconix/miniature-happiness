<?php
namespace tests\stubs\models;

if (! class_exists('\core\models\Model')) {
    require (NIV . 'core/models/Model.inc.php');
}
if (! class_exists('\core\models\Groups')) {
    require (NIV . 'core/models/Groups.inc.php');
}
if (! class_exists('\core\services\Session')) {
    require (NIV . 'core/services/Session.inc.php');
}

class DummyGroups extends \core\models\Groups
{

    public function __construct(\core\models\data\DataGroup $model_DataGroup)
    {
        $this->model_DataGroup = $model_DataGroup;
    }

    /**
     * Gets the user access level for current group
     * Based on the controller
     *
     * @param int $i_userid
     *            The user ID
     * @return int The access level defined in /include/services/Session.inc.php
     */
    public function getLevel($i_userid, $i_groupid = -1)
    {
        \core\Memory::type('int', $i_userid);
        
        return \core\services\Session::USER;
    }

    /**
     * Gets the user access level for the given group
     *
     * @param int $i_groupid
     *            The group ID
     * @param int $i_userid
     *            The user ID
     * @return int The access level defined in /include/services/Session.inc.php
     */
    public function getLevelByGroupID($i_groupid, $i_userid)
    {
        \core\Memory::type('int', $i_groupid);
        \core\Memory::type('int', $i_userid);
        
        return \core\services\Session::USER;
    }
}
?>