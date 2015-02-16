<?php
namespace core\models;

/**
 * Group model.
 * Contains the group data
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 05/05/2014
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
 *       
 * @see include/services/Session.inc.php
 * @see include/models/data/Data_Group.inc.php
 */
class Groups extends Model
{

    protected $model_DataGroup;

    protected $a_groups;

    protected $model_Config;

    /**
     * PHP5 constructor
     *
     * @param \core\services\QueryBuilder $service_QueryBuilder
     *            The query builder
     * @param \core\services\Security $service_Security
     *            The security service
     * @param \core\models\data\Data_Group $model_DataGroup
     *            The group data model
     * @param \core\models\Config $model_Config
     *            The config model
     */
    public function __construct(\core\services\QueryBuilder $service_QueryBuilder, \core\services\Security $service_Security, \core\models\data\Data_Group $model_DataGroup, \core\models\Config $model_Config)
    {
        parent::__construct($service_QueryBuilder, $service_Security);
        
        $this->model_DataGroup = $model_DataGroup;
        $this->model_Config = $model_Config;
        
        $this->a_groups = array();
        
        /* Load group-names */
        $this->service_QueryBuilder->select('groups', '*')->order('id');
        $service_Database = $this->service_QueryBuilder->getResult();
        
        $a_groups = $service_Database->fetch_assoc();
        foreach ($a_groups as $a_group) {
            $model = $this->model_DataGroup->cloneModel();
            $model->setData($a_group);
            $this->a_groups[$a_group['id']] = $model;
            
            $s_name = strtoupper($a_group['name']);
            if (! defined('GROUP_' . $s_name)) {
                define('GROUP_' . $s_name, (int) $a_group['id']);
            }
        }
    }

    /**
     * Gets all the registrated groups
     *
     * @return Data_Group-array The registrated groups
     */
    public function getGroups()
    {
        return $this->a_groups;
    }

    /**
     * Gets the registrated group with the given ID
     *
     * @param int $i_groupid
     *            The group ID
     * @return Data_Group The registrated group
     * @throws TypeException if $i_groupid is not a int
     * @throws MemoryException if the group does not exist
     */
    public function getGroup($i_groupid)
    {
        \core\Memory::type('int', $i_groupid);
        
        if (! array_key_exists($i_groupid, $this->a_groups)) {
            throw new \MemoryException("Calling non existing group with id " . $i_groupid);
        }
        
        return $this->a_groups[$i_groupid];
    }

    /**
     * Gets the user access level for current group
     * Based on the controller
     *
     * @param int $i_userid
     *            The user ID
     * @return int The access level defined in /include/services/Session.inc.php
     */
    public function getLevel($i_userid)
    {
        \core\Memory::type('int', $i_userid);
        
        $s_page = $this->model_Config->getPage();
        $this->service_QueryBuilder->select('group_pages', 'groupID')
            ->getWhere()
            ->addAnd('page', 's', $s_page);
        $service_Database = $this->service_QueryBuilder->getResult();
        
        if ($service_Database->num_rows() > 0) {
            $i_groupid = (int) $service_Database->result(0, 'groupID');
            return $this->getLevelByGroupID($i_groupid, $i_userid);
        }
        
        return \core\services\Session::ANONYMOUS;
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
        
        $this->service_QueryBuilder->select('group_users', 'level')
            ->getWhere()
            ->addAnd(array(
            'userid',
            'groupID'
        ), array(
            'i',
            'i'
        ), array(
            $i_userid,
            $i_groupid
        ));
        $service_Database = $this->service_QueryBuilder->getResult();
        
        if ($service_Database->num_rows() > 0) {
            return $service_Database->result(0, 'level');
        }
        
        return \core\services\Session::ANONYMOUS;
    }

    /**
     * Generates a new group
     *
     * @return Data_Group new group
     */
    public function generateGroup()
    {
        return $this->model_DataGroup->cloneModel();
    }

    /**
     * Gets the groups with level from the given user
     *
     * @param int $i_userid
     *            The userid
     * @return array The users groups with level
     */
    public function getGroupsLevel($i_userid)
    {
        \core\Memory::type('int', $i_userid);
        
        $a_groups = array();
        foreach ($this->a_groups as $obj_group) {
            $a_groups[$obj_group->getID()] = $obj_group->getLevelByGroupID($i_userid);
        }
        
        return $a_groups;
    }

    /**
     * Adds a user to the default groups
     *
     * @param int $i_userid
     *            The userid
     * @param int $i_level
     *            The requested level (0|1|2)
     */
    public function addUserDefaultGroups($i_userid, $i_level = 0)
    {
        \core\Memory::type('int', $i_userid);
        \core\Memory::type('int', $i_level);
        
        foreach ($this->a_groups as $obj_group) {
            if (! $obj_group->isDefault()) {
                continue;
            }
            
            $obj_group->addUser($i_userid, $i_level);
        }
    }

    /**
     * Deletes a user from all the groups
     *
     * @param int $i_userid
     *            The userid
     */
    public function deleteUserFromGroups($i_userid)
    {
        \core\Memory::type('int', $i_userid);
        
        foreach ($this->a_groups as $obj_group) {
            $obj_group->deleteUser($i_userid);
        }
    }

    /**
     * Edits the access levels for the given groups
     *
     * @param int $i_userid            
     * @param array $a_groups            
     * @param int $i_level
     *            level
     * @throws IllegalArgumentException the group ID does niet exist
     */
    public function editUserLevel($i_userid, $a_groups, $i_level)
    {
        \core\Memory::type('int', $i_userid);
        \core\Memory::type('array', $a_groups);
        \core\Memory::type('int', $i_level);
        
        foreach ($a_groups as $i_group) {
            if (! is_int($i_group) || ! array_key_exists($i_group, $this->a_groups)) {
                throw new \IllegalArgumentException("Only IDs of the members of Data_Group are accepted");
            }
            
            $this->a_groups[$i_group]->editUser($i_userid, $i_level);
        }
    }
}
?>
