<?php

namespace stats;

/**
 * @author    Rachelle Scheijen                                                
 * @since     1.0
 */
define('PROCESS', '1');
define('NIV', '../');
define('DS', DIRECTORY_SEPARATOR);

require(NIV . 'vendor' . DS . 'youconix' . DS . 'core' . DS . 'bootstrap.php');

class Stats
{

  /**
   * 
   * @var \Security
   */
  private $security;

  /**
   *
   * @var \Session
   */
  private $session;

  /**
   * 
   * @var \youconix\core\repositories\Stats
   */
  private $stats;

  /**
   * 
   * @var \Config
   */
  private $config;
  private $a_data;
  private $s_page;
  private $s_reference = '';
  private $s_browser;
  private $s_browserVersion;
  private $s_OS;
  private $s_OsType;
  private $i_colors = null;
  private $i_width = null;
  private $i_height = null;

  /**
   * PHP 5 constructor
   */
  public function __construct(\Security $security, \Session $session,
			      \youconix\core\repositories\Stats $stats, \Config $config)
  {
    $this->security = $security;
    $this->stats = $stats;
    $this->config = $config;
    $this->session = $session;

    $this->init();

    if (!$this->ignoreIP() && !$this->ignorePage() && !$this->isBot()) {
      $this->parse();

      $this->save();
    }

    $this->image();
  }

  /**
   * Inits the class Stats
   * Collects the information from the client
   */
  private function init()
  {
    $a_init_get = array(
	'colors' => 'int',
	'width' => 'int',
	'height' => 'int',
	'page' => 'string-DB'
    );

    $this->a_data = $this->security->secureInput('GET', $a_init_get);
    $this->s_page = $this->a_data['page'];
  }

  /**
   * 
   * @return boolean
   */
  private function ignoreIP()
  {
    $ips = []; //'127.0.0.1','::1'];
    $ranges = [];

    $settings = $this->config->getSettings();
    $ignore = ($settings->exists('statistics/ignore') ? $settings->get('statistics/ignore') : '');
    $data = explode(',', $ignore);
    foreach ($data as $item) {
      if (strpos($item, '*') === false) {
	$ips[] = $item;
	continue;
      }
      $parts = explode('*', $item);
      $ranges[] = $parts[0];
    }

    $visitorIP = $_SERVER['REMOTE_ADDR'];
    foreach ($ips as $ip) {
      if ($ip == $visitorIP) {
	return true;
      }
    }
    foreach ($ranges as $range) {
      if (substr($visitorIP, 0, strlen($range)) == $range) {
	return true;
      }
    }
    return false;
  }

  /**
   * 
   * @return boolean
   */
  private function ignorePage()
  {
    if (($this->s_page == '/logout') || (substr($this->s_page, 0, 6) == '/login')) {
      return true;
    }
    return false;
  }

  /**
   * 
   * @return boolean
   */
  private function isBot()
  {
    if (preg_match('/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl|facebookexternalhit/i',
		   $_SERVER['HTTP_USER_AGENT'])) {
      return true;
    }
    return false;
  }

  private function parse()
  {
    if (array_key_exists('colors', $this->a_data)) {
      $this->i_colors = $this->a_data['colors'];
      $this->i_width = $this->a_data['width'];
      $this->i_height = $this->a_data['height'];
    }

    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
      $this->s_reference = $this->security->secureStringDB($_SERVER['HTTP_REFERER']);
    }

    $s_useragent = $this->security->secureStringDB($_SERVER['HTTP_USER_AGENT']);

    $this->OS($s_useragent);
    $this->browser($s_useragent);
  }

  private function OS($s_useragent)
  {
    if (stripos($s_useragent, 'Linux') !== false) {
      $this->OSLinux($s_useragent);
    } elseif (stripos($s_useragent, 'Windows') != false) {
      $this->OSWindows($s_useragent);
    } elseif (stripos($s_useragent, 'iPhone') !== false) {
      $this->s_OS = 'iPhone';
      $this->s_OsType = 'iOS';
    } elseif (stripos($s_useragent, 'iPad') !== false) {
      $this->s_OS = 'iPad';
      $this->s_OsType = 'iOS';
    } elseif (stripos($s_useragent, 'Mac OS X') !== false) {
      $matches = null;
      preg_match('/Mac OS X 10_([0-9]+)/si', $s_useragent, $matches);
      $this->s_OS = 'OS X 10.' . $matches[1];
      $this->s_OsType = 'OS X';
    } elseif (stripos($s_useragent, 'CrOS') !== false) {
      $this->s_OS = 'Chrome OS';
      $this->s_OsType = 'Linux';
    } elseif (stripos($s_useragent, 'OpenBSD') !== false) {
      $this->s_OS = 'OpenBSD';
      $this->s_OsType = 'BSD';
    } else {
      $this->s_OS = 'Unknown';
      $this->s_OsType = 'Unknown';
    }
  }

  /**
   * @param string $s_useragent
   */
  private function OSLinux($s_useragent)
  {
    if (stripos($s_useragent, 'Android') !== false) {
      $this->s_OS = 'Android';
      $this->s_OsType = 'Linux';
    } elseif (stripos($s_useragent, 'Ubuntu') !== false) {
      $this->s_OS = 'Ubuntu';
      $this->s_OsType = 'Linux';
    } else {
      $this->s_OS = 'Other';
      $this->s_OsType = 'Linux';
    }
  }

  /**
   * 
   * @param string $s_useragent
   */
  private function OSWindows($s_useragent)
  {
    $matches = null;
    $this->s_OsType = 'Windows';

    if (stripos($s_useragent, 'Windows NT 5') != false) {
      $this->s_OS = 'Windows XP';
    } elseif (stripos($s_useragent, 'Windows NT 6.0') != false) {
      $this->s_OS = 'Windows Vista';
    } elseif (stripos($s_useragent, 'Windows NT 6.1') != false) {
      $this->s_OS = 'Windows 7';
    } elseif (stripos($s_useragent, 'Windows NT 6.2') != false) {
      $this->s_OS = 'Windows 8';
    } elseif (stripos($s_useragent, 'Windows NT 6.3') != false) {
      $this->s_OS = 'Windows 8.1';
    } elseif (stripos($s_useragent, 'Windows NT 10.') != false) {
      $this->s_OS = 'Windows 10';
    } elseif (stripos($s_useragent, 'Windows Phone') !== false) {
      preg_match('/windows Phone ([0-9]+)/si', $s_useragent, $matches);
      $this->s_OS = 'Windows Phone ' . $matches[1];
    } else {
      $this->s_OS = 'other';
    }
  }

  private function browser($s_useragent)
  {
    $matches = null;
    if (stripos($s_useragent, 'Firefox') !== false) {
      preg_match('/Firefox\/([0-9]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
      $this->s_browser = 'Firefox';
    } elseif (stripos($s_useragent, 'Opera') !== false) {
      preg_match('/Version\/([0-9]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
      $this->s_browser = 'Opera';
    } elseif ((stripos($s_useragent, 'Trident') !== false) || (stripos($s_useragent,
								       'Edge/') !== false)) {
      $this->browserIE($s_useragent);
    } elseif (stripos($s_useragent, 'Chromium') !== false) {
      $this->s_browser = 'Chromium';
      preg_match('/Chromium\/([0-9]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
    } elseif (stripos($s_useragent, 'Chrome') !== false) {
      $this->s_browser = 'Chrome';
      preg_match('/Chrome\/([0-9]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
    } elseif (stripos($s_useragent, 'Safari') !== false) {
      preg_match('/Version\/([0-9]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
      $this->s_browser = 'Safari';
    } else {
      $this->s_browser = 'Unknown';
      $this->s_browserVersion = 'Unknown';
    }
  }

  /**
   * 
   * @param string $s_useragent
   */
  private function browserIE($s_useragent)
  {
    $matches = null;
    if (stripos($s_useragent, 'MSIE') !== false) {
      preg_match('/MSIE ([0-9]+\.[0-9]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
      $this->s_browser = 'Internet Explorer';
    } elseif (stripos($s_useragent, 'Edge/') !== false) {
      preg_match('/Edge\/([^.]+\.[^\s]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
      $this->s_browser = 'Edge';
    } else { // Internet Explorer 11 & 12
      preg_match('/rv:([0-9]+\.[0-9]+)/si', $s_useragent, $matches);
      $this->s_browserVersion = $matches[1];
      $this->s_browser = 'Internet Explorer';
    }
  }

  /**
   * Saves the statistics
   */
  private function save()
  {
    $this->stats->savePageHit($this->s_page);
    if ($this->session->exists('statistics')) {
      return;
    }

    $this->session->set('statistics', 1);
    $fingerprint = $this->session->getFingerprint(true);

    if (!$this->stats->saveVisit($fingerprint)) {
      return;
    }

    /* Unique visitor */
    $this->stats->saveOS($this->s_OS, $this->s_OsType);
    $this->stats->saveBrowser($this->s_browser, $this->s_browserVersion);

    if (trim($this->s_reference) != '') {
      $this->stats->saveReference($this->s_reference);
    }

    if (!is_null($this->i_colors)) {
      $this->stats->saveScreenSize($this->i_width, $this->i_height);
      $this->stats->saveScreenColors($this->i_colors . '');
    }
  }

  /**
   * Displays the dummy image
   */
  private function image()
  {
    $s_styledir = $this->config->getSharedStylesDir();
    $s_file = WEB_ROOT . '/' . $s_styledir . '/images/stats.png';

    header('Content-type: image/png');
    header('Content-Transfer-Encoding: binary');
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header('Content-Length: ' . filesize($s_file));
    readfile($s_file);
  }
}

\Loader::inject('stats\Stats');
