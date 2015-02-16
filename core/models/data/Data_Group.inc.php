<?php
namespace core\models\data;

/**
 * Group data model.
 * Contains the group data
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *
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
class Data_Group extends \core\models\Model
{

    private $i_id;

    protected $s_name;

    protected $i_default = 0;

    private $a_users;

    protected $s_description;

    /**
     * PHP5 constructor
     *
     * @param \core\services\QueryBuilder $service_QueryBuilder
     *            The query builder
     * @param \core\services\Security $service_Security
     *            The security service
     */
    public function __construct(\core\services\QueryBuilder $service_QueryBuilder, \core\services\Security $service_Security)
    {
        parent::__construct($service_QueryBuilder, $service_Security);
        
        $this->a_validation = array(
            's_name' => array(
                'type' => 'string',
                'required' => 1
            ),
            'i_default' => array(
                'type' => 'enum',
                'set' => array(
                    0,
                    1
                )
            ),
            's_description' => array(
                'type' => 'string',
                'required' => 1
            )
        );
    }

    /**
     * Sets the group data
     *
     * @param array $a_data
     *            group data
     */
    public function setData($a_data)
    {
        $this->i_id = $a_data['id'];
        $this->s_name = $a_data['name'];
        $this->i_default = $a_data['automatic'];
        $this->s_description = $a_data['description'];
    }

    /**
     * Returns the ID
     *
     * @return int ID
     */
    public function getID()
    {
        return $this->i_id;
    }

    /**
     * Returns the name
     *
     * @return string name
     */
    public function getName()
    {
        return $this->s_name;
    }

    /**
     * Sets the name
     *
     * @param string $s_name            
     */
    public function setName($s_name)
    {
        \core\Memory::type('string', $s_name);
        
        $this->s_name = $s_name;
    }

    /**
     * Returns the description
     *
     * @return string description
     */
    public function getDescription()
    {
        return $this->s_description;
    }

    /**
     * Sets the description
     *
     * @param string $s_description            
     */
    public function setDescription($s_description)
    {
        \core\Memory::type('string', $s_description);
        
        $this->s_description = $s_description;
    }

    /**
     * Returns if the group is default
     *
     * @return boolean if the group is default
     */
    public function isDefault()
    {
        return $this->i_default == 1;
    }

    /**
     * Sets the group as default
     *
     * @param boolean $bo_default
     *            to true to make the group default
     */
    public function setDefault($bo_default)
    {
        \core\Memory::type('boolean', $bo_default);
        
        if ($bo_default) {
            $this->i_default = 1;
        } else {
            $this->i_default = 0;
        }
    }

    /**
     * Gets the user access level
     *
     * @param int $i_userid
     *            The user ID
     * @return int The access level defined in /include/services/Session.inc.php
     */
    public function getLevelByGroupID($i_userid)
    {
        \core\Memory::type('int', $i_userid);
        
        if (! is_null($this->a_users)) {
            if (array_key_exists($i_userid, $this->a_users)) {
                return $this->a_users[$i_userid];
            }
        } else {
            $this->a_users = array();
        }
        
        /* Get groupname */
        $this->service_QueryBuilder->select('group_users', 'level')
            ->getWhere()
            ->addAnd(array(
            'groupID',
            'userid'
        ), array(
            'i',
            'i'
        ), array(
            $this->i_id,
            $i_userid
        ));
        $service_Database = $this->service_QueryBuilder->getResult();
        
        if ($service_Database->num_rows() == 0) {
            /* No record found. Access denied */
            $this->a_users[$i_userid] = \core\services\Session::ANONYMOUS;
        } else {
            $this->a_users[$i_userid] = $service_Database->result(0, 'level');
        }
        
        return $this->a_users[$i_userid];
    }

    /**
     * Gets all the members from the group
     *
     * @return array The members from the group
     */
    public function getMembersByGroup()
    {
        $this->service_QueryBuilder->select('group_users g', 'g.level,u.nick')
            ->innerJoin('users u', 'g.userid', 'u.id')
            ->order('u.nick', 'ASC');
        $this->service_QueryBuilder->getWhere()->addAnd('g.groupID', 'i', $this->i_id);
        $service_Database = $this->service_QueryBuilder->getResult();
        
        $a_result = array();
        if ($service_Database->num_rows() > 0) {
            $a_result = $service_Database->fetch_assoc();
        }
        
        return $a_result;
    }

    /**
     * Saves the new group
     */
    public function save()
    {
        if (! is_null($this->i_id)) {
            return;
        }
        $this->performValidation();
        
        /* Get max Id */
        $this->service_QueryBuilder->select('groups', $this->service_QueryBuilder->getMaximun('id', 'id'));
        $this->i_id = $this->service_QueryBuilder->getResult()->result(0, 'id') + 1;
        
        $this->service_QueryBuilder->insert('groups', array(
            'id',
            'name',
            'description',
            'automatic'
        ), array(
            'i',
            's',
            's',
            's'
        ), array(
            $this->i_id,
            $this->s_name,
            $this->s_description,
            $this->i_automatic
        ));
        $i_groupID = $this->service_QueryBuilder->getResult()->getID();
        
        if ($this->i_automatic == 1) {
            /* Add users to group */
            $this->service_QueryBuilder->select('users', 'id,staff');
            $a_users = $this->service_QueryBuilder->getResult()->fetch_assoc();
            
            foreach ($a_users as $a_user) {
                $i_level = 0;
                if ($a_user['staff'] == \core\services\Session::ADMIN)
                    $i_level = 2;
                
                $this->service_QueryBuilder->insert('group_users', array(
                    'groupID',
                    'userid',
                    'level'
                ), array(
                    'i',
                    'i',
                    's'
                ), array(
                    $i_groupID,
                    $a_user['id'],
                    $i_level
                ));
                $this->service_QueryBuilder->getResult();
            }
        }
    }

    /**
     * Saves the changed group
     */
    public function persist()
    {
        if (is_null($this->i_id)) {
            return;
        }
        $this->performValidation();
        
        $this->service_QueryBuilder->update('groups', array(
            'name',
            'description',
            'automatic'
        ), array(
            's',
            's',
            's',
            'i'
        ), array(
            $this->s_name,
            $this->s_description,
            $this->i_default,
            $this->i_id
        ))->getResult();
    }

    /**
     * Deletes the group
     */
    public function deleteGroup()
    {
        /* Check if group is in use */
        if ($this->inUse()) {
            return;
        }
        
        $this->service_QueryBuilder->delete("group_users")
            ->getWhere()
            ->addAnd('groupID', 'i', $this->i_id);
        $this->service_QueryBuilder->getResult();
        $this->service_QueryBuilder->delete("groups")
            ->getWhere()
            ->addAnd('id', 'i', $this->i_id);
        $this->service_QueryBuilder->getResult();
    }

    /**
     * Adds a user to the group
     *
     * @param int $i_userid
     *            userid
     * @param int $i_level
     *            access level, default 0 (user)
     */
    public function addUser($i_userid, $i_level = 0)
    {
        \core\Memory::type('int', $i_userid);
        \core\Memory::type('int', $i_level);
        
        if ($i_level < 0 || $i_level > 2)
            $i_level = 0;
        
        if ($this->getLevelByGroupID($i_userid) == \core\services\Session::ANONYMOUS) {
            $this->service_QueryBuilder->insert("group_users", array(
                'groupID',
                'userid',
                'level'
            ), array(
                'i',
                'i',
                's'
            ), array(
                $this->i_id,
                $i_userid,
                $i_level
            ))->getResult();
        }
    }

    /**
     * Edits the users access rights for this group
     *
     * @param int $i_userid
     *            userid
     * @param int $i_level
     *            access level, default 0 (user)
     */
    public function editUser($i_userid, $i_level = 0)
    {
        \core\Memory::type('int', $i_userid);
        \core\Memory::type('int', $i_level);
        
        if (! in_array($i_level, array(
            - 1,
            0,
            1,
            2
        )))
            return;
        
        if ($i_level == - 1) {
            $this->service_QueryBuilder->delete("group_users")
                ->getWhere()
                ->addAnd('userid', 'i', $i_userid);
            $this->service_QueryBuilder->getResult();
        } else 
            if ($this->getLevelByGroupID($i_userid) == \core\services\Session::ANONYMOUS) {
                $this->service_QueryBuilder->insert("group_users", array(
                    'groupID',
                    'userid',
                    'level'
                ), array(
                    'i',
                    'i',
                    's'
                ), array(
                    $this->i_id,
                    $i_userid,
                    $i_level
                ))->getResult();
            } else {
                $this->service_QueryBuilder->update("group_users", 'level', 's', $i_level)
                    ->getWhere()
                    ->addAnd('userid', 'i', $i_userid);
                $this->service_QueryBuilder->getResult();
            }
    }

    /**
     * Adds all the users to this group if the group is default
     */
    public function addUsersToDefault()
    {
        if ($this->i_default == 0)
            return;
        
        $a_users = Memory::models('Users')->getUserIDs();
        $a_currentUsers = array();
        $this->service_QueryBuilder->select("group_users", "userid")
            ->getWhere()
            ->addAnd("groupID", 'i', $this->i_id);
        $service_Database = $this->service_QueryBuilder->getResult();
        if ($service_Database->num_rows() > 0) {
            $a_currentUsers = $service_Database->fetch_assoc_key('userid');
        }
        $i_level = \core\services\Session::USER;
        
        foreach ($a_users as $i_user) {
            if (array_key_exists($i_user, $a_currentUsers))
                continue;
            
            $this->service_QueryBuilder->insert("group_users", array(
                'groupID',
                'userid',
                'level'
            ), array(
                'i',
                'i',
                'i'
            ), array(
                $this->i_id,
                $i_user,
                $i_level
            ))->getResult();
        }
    }

    /**
     * Deletes the user from the group
     *
     * @param int $i_userid
     *            userid
     */
    public function deleteUser($i_userid)
    {
        \core\Memory::type('int', $i_userid);
        
        if ($this->getLevelByGroupID($i_userid) != \core\services\Session::ANONYMOUS) {
            $this->service_QueryBuilder->delete('group_users')
                ->getWhere()
                ->addAnd(array(
                'groupID',
                'userid'
            ), array(
                'i',
                'i'
            ), array(
                $this->i_id,
                $i_userid
            ));
            $this->service_QueryBuilder->getResult();
        }
    }

    /**
     * Checks if the group is in use
     *
     * @return boolean if the group is in use
     */
    public function inUse()
    {
        if (is_null($this->i_id))
            return false;
        
        $this->service_QueryBuilder->select('group_users', 'id')
            ->getWhere()
            ->addAnd('groupID', 'i', $this->i_id);
        $service_Database = $this->service_QueryBuilder->getResult();
        if ($service_Database->num_rows() == 0)
            return false;
        
        return true;
    }
}
?>