<?php

class Console
{

  /**
   *
   * @var \Headers
   */
  private $headers;

  /**
   *
   * @var \ConfigReader
   */
  private $configReader;

  /**
   *
   * @var array
   */
  private $knownCommands = [];

  /**
   *
   * @var string
   */
  private $command;

  /**
   *
   * @var string
   */
  private $commandFunction;
  private $cli;

  public function __construct()
  {
    $dir = str_replace('console.php', '', __FILE__);
    define('NIV', $dir);
    require (NIV . 'vendor/youconix/core/bootstrap.php');

    $this->headers = \Loader::inject('\Headers');
    $this->configReader = \Loader::inject('\ConfigReader');

    $this->run();
  }

  public function run()
  {
    $this->checkCli();

    if ($_SERVER['argc'] == 1) {
      echo('Missing cli command.' . PHP_EOL);
      return 1;
    }

    $this->readCommands();

    if (!$this->commandExist()) {
      echo('Unknown command ' . $this->command . '.' . PHP_EOL);
      return 1;
    }

    try {
      $call = $this->commandFunction;
      $this->cli->$call();
    } catch (Exception $ex) {
      echo('Error: ' . $ex->getMessage() . PHP_EOL);
      return 1;
    }

    return 0;
  }

  private function checkCli()
  {
    if (!(php_sapi_name() == 'cli' || (isset($_SERVER['argc']) && is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0))) {
      $this->headers->http400();
      $this->headers->printHeaders();
      echo('Console can only be called from the cli.');
      exit();
    }
  }

  private function readCommands()
  {
    try {
      $this->configReader->loadConfig('cli_commands');

      $this->knownCommands = $this->configReader->getBlock('');
    } catch (Exception $ex) {
      
    }
  }

  protected function commandExist()
  {
    if (strpos($_SERVER['argv'][1], ':') === false) {
      echo('Missing valid cli command.' . PHP_EOL);
      return 1;
    }
    $command = explode(':', $_SERVER['argv'][1]);
    $this->command = $command[0];
    $this->commandFunction = $command[1];

    if (!array_key_exists($this->command, $this->knownCommands)) {
      return false;
    }

    $this->cli = \Loader::inject($this->knownCommands[$this->command]);
    if (is_null($this->cli) ||
	!($this->cli instanceof \youconix\core\templating\CliController) ||
	!method_exists($this->cli, $this->commandFunction)) {
      return false;
    }

    return true;
  }
}

new Console();
