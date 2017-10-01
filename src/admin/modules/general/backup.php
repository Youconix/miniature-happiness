<?php
namespace admin\modules\general;

/**
 * Admin backup class
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Backup extends \admin\AdminController
{

    /**
     *
     * @var \youconix\core\services\Backup
     */
    private $backup;

    /**
     * Starts the class Groups
     *
     * @param \Request $request
     * @param \Config $config
     * @param \Language $language
     * @param \Output $template
     * @param \Logger $logs
     * @param \Headers $headers
     * @param \youconix\core\services\Backup $backup
     */
    public function __construct(\Request $request, \Config $config, \Language $language, \Output $template, \Logger $logs, \Headers $headers, \youconix\core\services\Backup $backup)
    {
        parent::__construct($request, $language, $template, $logs, $headers);
        
        $this->backup = $backup;
    }

    /**
     *
     * @return \Output
     */
    public function createBackupscreen()
    {
        $template = $this->createView('general/backup/createBackupscreen');
        
        $template->set('moduleTitle', 'Create backup');
        $template->set('title', 'Creating backup in progress');
        $template->set('backupText', 'This screen wil automatically refresh. Please wait...');
        $template->set('errorText', 'Creating the backup failed.');
        
        return $template;
    }

    /**
     * Creates the backup from the database and the settings
     *
     * @return \Output
     */
    public function createPartialBackup()
    {
        $template = $this->createView('general/backup/createPartialBackup');
        
        $s_backup = $this->backup->createPartialBackup();
        
        (is_null($s_backup)) ? $bo_result = 0 : $bo_result = 1;
        
        $template->set('result', $bo_result);
        $template->set('backup', $s_backup);
        
        return $template;
    }

    /**
     * Creates the full backup
     *
     * @return \Output
     */
    public function createFullBackup()
    {
        $template = $this->createView('general/backup/createFullBackup');
        
        $s_backup = $this->backup->createBackupFull();
        
        (is_null($s_backup)) ? $bo_result = 0 : $bo_result = 1;
        
        $template->set('result', $bo_result);
        $template->set('backup', $s_backup);
        
        return $template;
    }

    public function download()
    {
        if (! $this->get->has('file')) {
            throw new \Http404Exception('');
        }
        
        $this->backup->download($this->get->get('file'));
    }
    
    public function removeBackups(){
        $this->backup->removeBackups();
    }
}