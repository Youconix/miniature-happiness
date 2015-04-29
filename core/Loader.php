<?php

/**
 * General class loader and dependency injection
 * 
 * @author Roxanna Lugtigheid
 * @link        http://www.php-fig.org/psr/psr-4/
 */
class Loader
{

    private static function getFileName($s_className)
    {
        $s_className = ltrim($s_className, '\\');
        $s_fileName = '';
        $s_namespace = '';
        if ($i_lastNsPos = strrpos($s_className, '\\')) {
            $s_namespace = substr($s_className, 0, $i_lastNsPos);
            $s_className = substr($s_className, $i_lastNsPos + 1);
            $s_fileName = str_replace('\\', DIRECTORY_SEPARATOR, $s_namespace) . DIRECTORY_SEPARATOR;
        }
        $s_fileName .= str_replace('_', DIRECTORY_SEPARATOR, $s_className) . '.php';
        
        if (file_exists(NIV . 'lib' . DIRECTORY_SEPARATOR . $s_fileName)) {
            return 'lib' . DIRECTORY_SEPARATOR . $s_fileName;
        }
        
        if (file_exists(NIV . $s_fileName)) {
            return $s_fileName;
        }
        
        $s_fileName = str_replace('.php', '.inc.php', $s_fileName);
        if (file_exists(NIV . $s_fileName)) {
            return $s_fileName;
        }
        
        if (defined('WEBSITE_ROOT') && file_exists(WEBSITE_ROOT . $s_fileName)) {
            return WEBSITE_ROOT . $s_fileName;
        }
        
        return null;
    }

    public static function autoload($s_className)
    {
        if (preg_match('/Exception$/', $s_className)) {
            $s_fileName = null;
            if (file_exists(NIV . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'exceptions' . DIRECTORY_SEPARATOR . $s_className . '.inc.php')) {
                $s_fileName = NIV . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'exceptions' . DIRECTORY_SEPARATOR . $s_className . '.inc.php';
            }
        } else {
            $s_fileName = Loader::getFileName($s_className);
        }
        
        if (! is_null($s_fileName)) {
            require NIV . $s_fileName;
        }
    }

    public static function Inject($s_className, $a_arguments = array())
    {        
        $s_fileName = Loader::getFileName($s_className);
        
        if (is_null($s_fileName)) {
            echo ('can not find file ' . $s_className);
            return null;
        }
        
        $s_caller = $s_className;
        
        if (strpos($s_fileName, 'core' . DIRECTORY_SEPARATOR) !== false) {
            // Check for override.
            $s_fileNameChild = str_replace('core' . DIRECTORY_SEPARATOR, 'includes' . DIRECTORY_SEPARATOR, $s_fileName);
            
            if (file_exists(NIV . $s_fileNameChild)) {
                $s_fileName = $s_fileNameChild;
                
                $s_caller = str_replace('core\\', 'includes\\', $s_className);
            }
        }
        
        if (substr($s_caller, 0, 1) != '\\') {
            $s_caller = '\\' . $s_caller;
        }
        
        $object = Loader::injection($s_caller, NIV . $s_fileName, $a_arguments);
        
        return $object;
    }

    /**
     * Performs the dependency injection
     *
     * @param String $s_caller
     *            class name
     * @param String $s_filename
     *            source file name
     * @throws RuntimeException the object is not instantiable.
     * @return Object called object
     */
    private static function injection($s_caller, $s_filename, $a_argumentsGiven)
    {
        $ref = new \ReflectionClass($s_caller);
        if (! $ref->isInstantiable()) {
            throw new \RuntimeException('Can not create a object from class ' . $s_caller . '.');
        }
        
        $bo_singleton = false;
        if (method_exists($s_caller, 'isSingleton') && $s_caller::isSingleton()) {
            /* Check cache */
            if (\core\Memory::IsInCache($s_caller)) {
                return \core\Memory::getCache($s_caller);
            } else {
                $bo_singleton = true;
            }
        }
        
        $a_matches = Loader::getConstructor($s_filename);
        
        if (count($a_matches) == 0) {
            /* No arguments */
            return new $s_caller();
        }
        $a_argumentNamesPre = explode(',', $a_matches[1]);
        
        $a_argumentNames = array();
        $a_arguments = array();
        foreach ($a_argumentNamesPre as $s_name) {
            $s_name = trim($s_name);
            if (strpos($s_name, ' ') === false) {
                continue;
            }
            if (substr($s_name, 0, 1) == '\\') {
                $s_name = substr($s_name, 1);
            }
            
            $a_item = explode(' ', $s_name);
            $a_argumentNames[] = $a_item[0];
        }
        
        foreach ($a_argumentNames as $s_name) {
            $a_path = explode('\\', $s_name);
            
            if (count($a_path) == 1) {
                /* No namespace */
                if (strpos($s_name, 'Helper_') !== false) {
                    $s_name = str_replace('Helper_', '', $s_name);
                    $a_arguments[] = \core\Memory::helpers($s_name);
                } else 
                    if (strpos($s_name, 'Service_') !== false) {
                        $s_name = str_replace('Service_', '', $s_name);
                        $a_arguments[] = \core\Memory::services($s_name);
                    } else 
                        if (strpos($s_name, 'Model_') !== false) {
                            $s_name = str_replace('Model_', '', $s_name);
                            $a_arguments[] = \core\Memory::models($s_name);
                        } else {
                            /* Try to load object */
                            $a_arguments[] = Loader::inject($s_name);
                        }
            } else {
                $a_arguments[] = Loader::inject($s_name);
                
                /*
                 * $s_name = end($a_path);
                 * $bo_data = false;
                 * if ($a_path[2] == 'data') {
                 * $bo_data = true;
                 * }
                 *
                 * if (($a_path[1] == 'helpers') || ($a_path[1] == 'html')) {
                 * $a_arguments[] = \core\Memory::helpers($s_name, $bo_data);
                 * } else
                 * if (($a_path[1] == 'services') || ($a_path[1] == 'database')) {
                 * $a_arguments[] = \core\Memory::services($s_name, $bo_data);
                 * } else
                 * if ($a_path[1] == 'models') {
                 * $a_arguments[] = \core\Memory::models($s_name, $bo_data);
                 * }
                 */
            }
        }
        
        $a_arguments = array_merge($a_arguments, $a_argumentsGiven);
        
        $object = $ref->newInstanceArgs($a_arguments);
        
        if ($bo_singleton) {
            \core\Memory::setCache($s_caller, $object);
        }
        
        return $object;
    }

    /**
     * Gets the constructor parameters
     *
     * @param String $s_filename
     *            name
     * @return array parameters
     */
    private static function getConstructor($s_filename)
    {
        $service_File = \core\Memory::getCache('\core\services\File');
        
        if ($service_File->exists($s_filename)) {
            $s_file = $service_File->readFile($s_filename);
        } else 
            if ($service_File->exists(str_replace('.inc.php', '.php', $s_filename))) {
                $s_file = $service_File->readFile(str_replace('.inc.php', '.php', $s_filename));
            } else {
                throw new Exception('Call to unknown file ' . $s_filename . '.');
            }
        
        if (stripos($s_file, '__construct') === false) {
            /* Check if file has parent */
            preg_match('#class\\s+[a-zA-Z0-9\-_]+\\s+extends\\s+([\\\a-zA-Z0-9_\-]+)#si', $s_file, $a_matches);
            if (count($a_matches) == 0) {
                return array();
            }
            
            switch ($a_matches[1]) {
                case '\core\models\Model':
                case 'Model':
                    $s_filename = NIV . 'core/models/Model.inc.php';
                    break;
                
                case '\core\services\Service':
                case 'Service':
                    $s_filename = NIV . 'core/services/Service.inc.php';
                    break;
                
                case '\core\helpers\Helper':
                case 'Helper':
                    $s_filename = NIV . 'core/helpers/Helper.inc.php';
                    break;
                
                default:
                    /* Check for namespace parent */
                    preg_match('#extends\\s+(\\\\{1}[\\\a-zA-Z0-9_\-]+)#si', $s_file, $a_matches2);
                    if (count($a_matches2) > 0) {
                        
                        if (strpos($a_matches2[1], '\core') !== false || strpos($a_matches2[1], '\includes') !== false) {
                            $s_filename = NIV . $a_matches2[1] . '.inc.php';
                        } else {
                            $s_filename = NIV . 'lib' . DIRECTORY_SEPARATOR . $a_matches2[1] . '.php';
                        }
                        $s_filename = str_replace(array(
                            '\\',
                            DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                        ), array(
                            DIRECTORY_SEPARATOR,
                            DIRECTORY_SEPARATOR
                        ), $s_filename);
                    } else {
                        /* Check for namespace */
                        preg_match('#namespace\\s+([\\a-z-_0-9]+);#', $s_file, $a_namespaces);
                        if (count($a_namespaces) > 0) {
                            $s_filename = NIV . str_replace('\\', '/', $a_namespaces[1] . '/' . $a_matches[1]) . '.inc.php';
                        } else {
                            $s_filename = NIV . str_replace('\\', '/', $a_matches[1]) . '.inc.php';
                        }
                    }
            }
            
            return Loader::getConstructor($s_filename);
        }
        
        preg_match('#function\\s+__construct\\s?\({1}\\s?([\\a-zA-Z\\s\$\-_,]+)\\s?\){1}#si', $s_file, $a_matches);
        
        return $a_matches;
    }
}

function loaderWrapper($s_className)
{
    Loader::autoload($s_className);
}

spl_autoload_register('loaderWrapper');