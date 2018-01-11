<?php

namespace admin\modules\statistics;

use \youconix\core\templating\AdminController as AdminController;

/**
 * Admin statistics view class
 *
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Stats extends AdminController
{

  /**
   *
   * @var \youconix\core\repositories\Stats
   */
  private $stats;
  
  /**
   *
   * @var \youconix\core\helpers\OnOff
   */
  private $onOff;
  
  /**
   *
   * @var \Settings
   */
  private $settings;
  
  private $i_startDate;
  private $i_endDate;
  private $colors;
  private $mainTitle;
  
  private $settingsEnabled;

  /**
   * Starts the class Stats
   *
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\repositories\Stats $stats
   * @param \youconix\core\helpers\OnOff $onOff
   * @param \Settings $settings
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\repositories\Stats $stats,
			      \youconix\core\helpers\OnOff $onOff,
			      \Settings $settings)
  {
    $this->stats = $stats;
    $this->onOff = $onOff;
    $this->settings = $settings;
    
    parent::__construct($wrapper);
  }

  /**
   * 
   * @param \Output $template
   */
  protected function setDefaultValues(\Output $template)
  {
    $template->set('title_startdate', date('d-m-Y', $this->i_startDate));
    $template->set('title_enddate', date('d-m-Y', $this->i_endDate));
    
    $template->set('amount', t('system/admin/stats/amount'));
    $template->set('pageTitle', t('system/admin/stats/title'));
    
    parent::setDefaultValues($template);
  }

  /**
   * Inits the class Stats
   */
  protected function init()
  {
    $this->init_post = [
	'enabled' => 'boolean',
	'ignore' => 'string'
    ];
    
    parent::init();

    $i_daysMonth = 30;//cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
    $this->i_startDate = mktime(0, 0, 0, date("n"), 1, date("Y") - 1);
    $this->i_endDate = mktime(23, 59, 59, date("n"), $i_daysMonth, date("Y"));

    $this->colors = array(
	'1252f3', // blue
	'b331ae', // purple
	'63413e', // brows
	'3a8555', // green,
	'f32212', // red
	'3fc0cf',
	'988680',
	'72ff00', // light green
	'111', // black,
	'941f1f',
	'599173'
    );
    
    $this->mainTitle = t('system/admin/statistics/mainTitle');
    $this->settingsEnabled = ($this->settings->exists('statistics/enabled') ? $this->settings->get('statistics/enabled') : 1);
  }
  
  /**
   * 
   * @param int $index
   * @return string
   */
  private function getColor($index)
  {
    $maxSize = (count($this->colors));
    if ($index > ($maxSize-1)){
      $index = ($index - $maxSize);
    }
    
    return $this->colors[$index];
  }
  
  /**
   * @Route("/controlpanel/statistics/settings", name="admin_statistics_settings")
   * @return \Output
   */
  public function settings()
  {
    $enabled = clone $this->onOff;
    $settingsEnabled = ($this->settings->exists('statistics/enabled') ? $this->settings->get('statistics/enabled') : 1);
    $ignore = ($this->settings->exists('statistics/ignore') ? $this->settings->get('statistics/ignore') : '');
    
    if ($settingsEnabled) {
      $enabled->setSelected(true);
    }
    $enabled->setName('enabled');
    
    $template = $this->createView('admin/modules/statistics/stats/settings');
    $template->set('settingsTitle', t('system/admin/statistics/settings'));
    $template->set('enabledText', 'Statistics enabled');
    $template->set('enabled', $enabled);
    $template->set('ignoreText', 'IP\'s to ignore. Separate with a ,');
    $template->set('ignore', $ignore);
    $template->set('saveButton', t('system/buttons/save'));
    $template->set('title', $this->mainTitle.t('system/admin/statistics/settings'));
    $this->setDefaultValues($template);
    
    $enabled->addHead($template);
    
    return $template;
  }
  
  /**
   * @Route("/controlpanel/statistics/settings/save", name="admin_statistics_settings_save")
   * @return \Output
   */
  public function settingsSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
	'enabled' => 'type:boolean|required',
	'ignore' => 'type:string'
    ])){
      $this->getHeaders()->http400();
      $this->getHeaders()->printHeaders();
      return;
    }
    
    $this->settings->set('statistics/enabled', $post->get('enabled'));
    $this->settings->set('statistics/ignore', $post->get('ignore'));
    $this->settings->save();
    
    $template = $this->settings();
    $template->set('settingsSaved', t('system/admin/statistics/settingsSaved'));
    
    return $template;
  }
  
  private function checkStatus(){
    if (!$this->settingsEnabled) {
      $this->getHeaders()->redirectPath('admin_statistics_settings');
    }
  }

  /**
   * @Route("/controlpanel/statistics/visitors", name="admin_statistics_visitors")
   * Displays the hits and unique visitors
   * @return \Output
   */
  public function visitors()
  {
    $this->checkStatus();
    
    $labels = [];
    $i_month = date('n', $this->i_startDate);
    for ($i = 1; $i <= 13; $i ++) {
      $labels[] = t('system/monthsShort/month' . $i_month);

      $i_month ++;
      if ($i_month == 13) {
	$i_month = 1;
      }
    }

    $a_visitors = [
	'visitors' => [],
	'unique' => [],
	'pages' => []
    ];
    $visitors = $this->stats->getVisitors($this->i_startDate, $this->i_endDate);
    $unique = $this->stats->getUnique($this->i_startDate, $this->i_endDate);
    $pages = $this->stats->getPageVisits($this->i_startDate, $this->i_endDate);
    
    foreach ($visitors as $i => $visitor) {
      $hitAmount = $visitor->getAmount();
      $uniqueAmount = $unique->current()->getAmount();
      $pageAmount = (array_key_exists($i, $pages) ? $pages[$i] : 0);
      
      if ($uniqueAmount < 0){
	$uniqueAmount = 0;
      }
      if (($pageAmount == 0) || ($hitAmount == 0)) {
	$pageAmount = 0;
      }
      else {
	$pageAmount = ceil($pageAmount/$hitAmount);
      }
      
      
      $unique->next();
      
      $a_visitors['visitors'][] = $hitAmount;
      $a_visitors['unique'][] = $uniqueAmount;
      $a_visitors['pages'][] = $pageAmount;
    }

    $template = $this->createView('admin/modules/statistics/stats/visitors');
    $template->set('color1', $this->getColor(0));
    $template->set('color2', $this->getColor(1));
    $template->set('color3', $this->getColor(2));
    $template->set('line1', t('system/admin/statistics/visitors'));
    $template->set('line2', t('system/admin/statistics/uniqueVisitors'));
    $template->set('line3', t('system/admin/statistics/pagesPerVisit'));
    
    $template->set('hitsTitle', t('system/admin/statistics/visitors'));
    $template->set('labels', $labels);
    $template->set('visitors', $a_visitors);
    $template->set('title', $this->mainTitle.t('system/admin/statistics/visitors'));
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Displays the visitors pro hour
   * @Route("/controlpanel/statistics/visitors_hour", name="admin_statistics_visitors_hour")
   * @return \Output
   */
  public function visitorsHours()
  {
    $this->checkStatus();
        
    $labels = array();
    for ($i = 0; $i <= 23; $i++) {
      $labels[] = $i;
    }

    $hits = $this->stats->getHitsHours($this->i_startDate, $this->i_endDate);

    $template = $this->createView('admin/modules/statistics/stats/hits_hours');
    $template->set('hitsTitle', t('system/admin/statistics/visitors_hours'));
    $template->set('labels', $labels);
    $template->set('hits', [$hits]);
    $template->set('color1', $this->getColor(0));
    $template->set('line1', t('system/admin/statistics/visitors'));
    $template->set('title', $this->mainTitle.t('system/admin/statistics/visitors_hours'));
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Displays the operating systems
   * @Route("/controlpanel/statistics/os", name="admin_statistics_os")
   * @return \Output
   */
  public function OS()
  {
    $this->checkStatus();
    
    $a_ossesRaw = $this->stats->getOS($this->i_startDate, $this->i_endDate);
    $a_lines = array();
    $a_osses = array();
    
    $i = 0;
    $a_types = array();
    foreach ($a_ossesRaw AS $a_os) {
      if (!in_array($a_os['type'], $a_types)) {
	$a_types[] = $a_os['type'];
	$i++;
      }

      $a_lines[] = array(
	  'color' => $this->getColor($i),
	  'type' => $a_os['type'],
	  'text' => $a_os['name']
      );

      $a_osses[] = array(
	  'amount' => $a_os['amount']
      );
    }

    $template = $this->createView('admin/modules/statistics/stats/os');
    $template->set('osTitle', t('system/admin/stats/OS'));
    $template->set('lines', $a_lines);
    $template->set('os', $a_osses);
    $template->set('title', $this->mainTitle.t('system/admin/stats/OS'));
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Displays the browsers
   * @Route("/controlpanel/statistics/browsers", name="admin_statistics_browsers")
   * @return \Output
   */
  public function browser()
  {
    $this->checkStatus();
    
    $a_browsersRaw = $this->stats->getBrowsers($this->i_startDate,
					       $this->i_endDate);
    $a_lines = array();
    $a_browsers = array();

    $i = -1;
    $a_types = array();
    foreach ($a_browsersRaw AS $a_browser) {
      if (!in_array($a_browser['type'], $a_types)) {
	$a_types[] = $a_browser['type'];
	$i++;
      }

      $a_lines[] = array(
	  'color' => $this->getColor($i),
	  'type' => $a_browser['type'],
	  'text' => $a_browser['name']
      );

      $a_browsers[] = array(
	  'amount' => $a_browser['amount']
      );
    }

    $template = $this->createView('admin/modules/statistics/stats/browser');
    $template->set('browserTitle', t('system/admin/stats/browsers'));
    $template->set('lines', $a_lines);
    $template->set('browsers', $a_browsers);
    $template->set('title', $this->mainTitle.t('system/admin/stats/browsers'));
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Displays the screen colors
   * @Route("/controlpanel/statistics/colors", name="admin_statistics_colors")
   * @return \Output
   */
  public function screenColors()
  {
    $this->checkStatus();
    
    $a_lines = array();
    $a_colors = array();
    $a_data = $this->stats->getScreenColors($this->i_startDate, $this->i_endDate);    
    krsort($a_data, SORT_STRING);

    $i = 0;
    foreach ($a_data as $a_color) {
      $a_lines[] = array(
	  'color' => $this->getColor($i),
	  'text' => $a_color['name']
      );

      $a_colors[] = [
	  'amount' => $a_color['amount']
      ];
      $i++;
    }

    $template = $this->createView('admin/modules/statistics/stats/screencolors');
    $template->set('screenColorsTitle', t('system/admin/stats/screenColors'));
    $template->set('lines', $a_lines);
    $template->set('screenColors', $a_colors);
    $template->set('title', $this->mainTitle.t('system/admin/stats/screenColors'));
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Displays the screen sizes
   * @Route("/controlpanel/statistics/sizes", name="admin_statistics_sizes")
   * @return \Output
   */
  public function screenSizes()
  {
    $this->checkStatus();
    
    $a_lines = array();
    $a_sizes = array();
    $a_data = $this->stats->getScreenSizes($this->i_startDate, $this->i_endDate);
    krsort($a_data, SORT_STRING);

    $i = 0;
    foreach ($a_data as $a_size) {
      $a_lines[] = array(
	  'color' => $this->getColor($i),
	  'text' => $a_size['width'] . 'X' . $a_size['height']
      );

      $a_sizes[] = [
	  'amount' => $a_size['amount']
      ];
      $i++;
    }

    $template = $this->createView('admin/modules/statistics/stats/screensizes');
    $template->set('screenSizesTitle',t('system/admin/stats/screenSizes'));
    $template->set('lines', $a_lines);
    $template->set('screenSizes', $a_sizes);
    $template->set('title', $this->mainTitle.t('system/admin/stats/screenSizes'));
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Displays the references
   * @Route("/controlpanel/statistics/references", name="admin_statistics_references")
   * @return \Output
   */
  public function references()
  {
    $this->checkStatus();
    
    $a_lines = array();
    $a_references = array();
    $a_data = $this->stats->getReferences($this->i_startDate, $this->i_endDate);
    krsort($a_data, SORT_STRING);

    $i = 0;
    foreach ($a_data as $a_reference) {
      $a_lines[] = array(
	  'color' => $this->getColor($i),
	  'text' => $a_reference['name']
      );

      $a_references[] = [
	  'amount' => $a_reference['amount']
      ];
      $i++;
    }

    $template = $this->createView('admin/modules/statistics/stats/references');
    $template->set('referencesTitle', t('system/admin/statistics/references'));
    $template->set('lines', $a_lines);
    $template->set('references', $a_references);
    $template->set('title', $this->mainTitle.t('system/admin/statistics/references'));
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Displays the pages hits
   * @Route("/controlpanel/statistics/pages", name="admin_statistics_pages")
   * @return \Output
   */
  public function pages()
  {
    $this->checkStatus();
    
    $a_lines = array();
    $a_pages = array();
    $a_data = $this->stats->getPages($this->i_startDate, $this->i_endDate);
    krsort($a_data, SORT_STRING);

    $i = 0;
    foreach ($a_data as $a_page) {
      $a_lines[] = array(
	  'color' => $this->getColor($i),
	  'text' => $a_page['name']
      );

      $a_pages[] = [
	  'amount' => $a_page['amount']
      ];
      $i++;
    }

    $template = $this->createView('admin/modules/statistics/stats/pages');
    $template->set('pagesTitle', t('system/admin/statistics/pages'));
    $template->set('lines', $a_lines);
    $template->set('pages', $a_pages);
    $template->set('title', $this->mainTitle.t('system/admin/statistics/pages'));
    $this->setDefaultValues($template);
    
    return $template;
  }
}
