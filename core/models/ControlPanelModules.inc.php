<?php
namespace core\models;

class ControlPanelModules.inc extends \core\models\Model
{

    private $service_File;

    private $service_XML;

    /**
     * PHP5 constructor
     *
     * @param \core\services\QueryBuilder $service_QueryBuilder
     *            The query builder
     * @param \core\services\Validation $service_Validation
     *            The validation service
     * @param \core\services\File $service_File
     *            The file service
     * @param \core\services\XML $service_XML
     *            The XML service
     */
    public function __construct(\core\services\QueryBuilder $service_QueryBuilder, \core\services\Validation $service_Validation, \core\services\File $service_File, \core\services\Xml $service_XML)
    {
        parent::__construct($service_QueryBuilder, $service_Validation);
        
        $this->service_File = $service_File;
        $this->service_XML = $service_XML;
    }

    /**
     * Returns the admin modules directory
     *
     * @return string The directory
     */
    public function getDirectory()
    {
        return NIV . 'admin' . DIRECTORY_SEPARATOR . 'modules';
    }

    /**
     * Returns the module names
     *
     * @return array The names
     */
    private function getModules()
    {
        $s_dir = $this->getDirectory();
        $a_directory = $this->service_File->readDirectory($s_dir, false, true);
        
        $a_files = array();
        foreach ($a_directory as $s_module) {
            if (! is_dir($s_dir . DIRECTORY_SEPARATOR . $s_module) || ! $this->service_File->exists($s_dir . DIRECTORY_SEPARATOR . $s_module . '/settings.xml')) {
                continue;
            }
            
            $a_files[] = $s_module;
        }
        
        return $a_files;
    }

    /**
     * Returns the names of the installed modules
     *
     * @return array The names
     */
    public function getInstalledModulesList()
    {
        $a_filesRaw = $this->getModules();
        $a_files = array();
        
        $this->service_QueryBuilder->select('admin_modules', 'name');
        $database = $this->service_QueryBuilder->getResult();
        
        if ($database->num_rows() > 0) {
            $data = $database->fetch_assoc();
            
            foreach ($data as $a_name) {
                if (in_array($a_name['name'], $a_filesRaw)) {
                    $a_files[] = $a_name['name'];
                }
            }
        }
        
        return $a_files;
    }

    /**
     * Returns the installed modules
     *
     * @return array The modules
     */
    public function getInstalledModules()
    {
        $a_modules = array(
            'installed' => array(),
            'upgrades' => array()
        );
        
        $this->service_QueryBuilder->select('admin_modules', '*');
        $database = $this->service_QueryBuilder->getResult();
        
        if ($database->num_rows() > 0) {
            $a_data = $database->fetch_assoc();
            $s_dir = $this->getDirectory();
            
            foreach ($a_data as $a_item) {
                $obj_settings = $this->service_XML->cloneService();
                $obj_settings->load($s_dir . DIRECTORY_SEPARATOR . $a_item['name'] . DIRECTORY_SEPARATOR . 'settings.xml');
                if ($obj_settings->get('module/version') > $a_item['version']) {
                    $a_item['versionNew'] = $obj_settings->get('module/version');
                    $a_modules['upgrades'][] = $a_item;
                } else {
                    $a_modules['installed'][] = $a_item;
                }
            }
        }
        
        return $a_modules;
    }

    /**
     * Returns the new (not installed) modules
     *
     * @return array The modules
     */
    public function getNewModules()
    {
        $a_filesRaw = $this->getModules();
        $a_files = array();
        
        $this->service_QueryBuilder->select('admin_modules', 'name');
        $database = $this->service_QueryBuilder->getResult();
        
        if ($database->num_rows() > 0) {
            $a_data = $database->fetch_row();
            $a_modules = array();
            foreach ($a_data as $a_item) {
                $a_modules[] = $a_item[0];
            }
            
            foreach ($a_filesRaw as $s_file) {
                if (! in_array($s_file, $a_modules)) {
                    $a_files[] = $s_file;
                }
            }
        }
        
        foreach ($a_files as $key => $s_file) {
            /* Get module data */
            $a_files[$key] = $this->getModuleData($s_file);
        }
        
        return $a_files;
    }

    /**
     * Returns the module data from the settings-file
     *
     * @param string $s_module
     *            The module name
     * @return array The name, author, version and description
     */
    public function getModuleData($s_module, $bo_full = false)
    {
        $a_data = array(
            'name' => '',
            'author' => '',
            'version' => '',
            'description' => ''
        );
        
        $s_dir = $this->getDirectory();
        
        $obj_settings = $this->service_XML->cloneService();
        $obj_settings->load($s_dir . DIRECTORY_SEPARATOR . $s_module . DIRECTORY_SEPARATOR . 'settings.xml');
        
        foreach ($a_data as $s_key => $value) {
            if ($obj_settings->exists('module/' . $s_key)) {
                $a_data[$s_key] = $obj_settings->get('module/' . $s_key);
            }
        }
        
        if ($bo_full) {
            $a_keys = array(
                'install',
                'upgrade',
                'deinstall'
            );
            foreach ($a_keys as $s_key) {
                $a_data[$s_key] = $obj_settings->get('module/' . $s_key);
            }
        }
        
        return $a_data;
    }

    /**
     * Installs the given module
     *
     * @param string $s_name
     *            The module name
     * @throws Exception If the module throws an exception
     */
    public function installModule($s_name)
    {
        /* Check if module not exists */
        $this->service_QueryBuilder->select('admin_modules', 'id')
            ->getWhere()
            ->addAnd('name', 's', $s_name);
        $database = $this->service_QueryBuilder->getResult();
        if ($database->num_rows() != 0) {
            return;
        }
        
        $s_dir = $this->getDirectory();
        if (! $this->service_File->exists($s_dir . DIRECTORY_SEPARATOR . $s_name . DIRECTORY_SEPARATOR . 'settings.xml')) {
            return;
        }
        
        $a_data = $this->getModuleData($s_name, true);
        
        $this->service_QueryBuilder->insert('admin_modules', array(
            'name',
            'installed',
            'author',
            'description',
            'version'
        ), array(
            's',
            'i',
            's',
            's',
            's'
        ), array(
            $a_data['name'],
            time(),
            $a_data['author'],
            $a_data['description'],
            $a_data['version']
        ));
        $this->service_QueryBuilder->getResult();
        
        if (! empty($a_data['install'])) {
            /* Run module installer */
            require ($s_dir . DIRECTORY_SEPARATOR . $s_name . DIRECTORY_SEPARATOR . $a_data['install']);
        }
    }

    /**
     * Removes the given module
     *
     * @param int $i_id
     *            The module ID
     * @throws Exception If the module throws an exception
     */
    public function removeModule($i_id)
    {
        /* Check if module exists */
        $this->service_QueryBuilder->select('admin_modules', 'name')
            ->getWhere()
            ->addAnd('name', 's', $s_name);
        $database = $this->service_QueryBuilder->getResult();
        if ($database->num_rows() == 0) {
            return;
        }
        
        $s_name = $database->result(0, 'name');
        
        if (in_array($s_name, array(
            'general',
            'settings',
            'statistics'
        ))) {
            /*
             * Framework modules
             * Do not remove
             */
        }
        
        $s_dir = $this->getDirectory();
        
        $this->service_QueryBuilder->delete('admin_modules')
            ->getWhere()
            ->addAnd('id', 'i', $i_id);
        $this->service_QueryBuilder->getResult();
        
        if ($this->service_File->exists($s_dir . DIRECTORY_SEPARATOR . $s_name . DIRECTORY_SEPARATOR . 'settings.xml')) {
            $a_data = $this->getModuleData($s_name, true);
            
            if (! empty($a_data['deinstall'])) {
                /* Run module deinstaller */
                require ($s_dir . DIRECTORY_SEPARATOR . $s_name . DIRECTORY_SEPARATOR . $a_data['deinstall']);
            }
        }
        
        $this->service_File->deleteDirectory($s_dir . DIRECTORY_SEPARATOR . $s_name);
    }

    /**
     * Upgrades the given module
     *
     * @param int $i_id
     *            The module ID
     * @throws Exception If the module throws an exception
     */
    public function updateModule($i_id)
    {
        /* Check if module exists */
        $this->service_QueryBuilder->select('admin_modules', 'name')
            ->getWhere()
            ->addAnd('name', 's', $s_name);
        $database = $this->service_QueryBuilder->getResult();
        if ($database->num_rows() == 0) {
            return;
        }
        
        $s_name = $database->result(0, 'name');
        $s_dir = $this->getDirectory();
        
        if (! $this->service_File->exists($s_dir . DIRECTORY_SEPARATOR . $s_name . DIRECTORY_SEPARATOR . 'settings.xml')) {
            return;
        }
        
        $this->service_QueryBuilder->update('admin_modules', 'version', 's', $a_data['version'])
            ->getWhere()
            ->addAnd('id', 'i', $i_id);
        $this->service_QueryBuilder->getResult();
        
        $a_data = $this->getModuleData($s_name, true);
        
        if (! empty($a_data['upgrade'])) {
            /* Run module upgrader */
            require ($s_dir . DIRECTORY_SEPARATOR . $s_name . DIRECTORY_SEPARATOR . $a_data['upgrade']);
        }
    }
}