<?php

use Globalis\Robo\Core\Command;
use Globalis\Robo\Core\SemanticVersion;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Globalis\Robo\Tasks
{

    /**
     * Répertoire contenant les variables de configuration
     * @var string
     */
    private $configDirectory = __DIR__.'/.robo/config/';

    /**
     * Répertoire contenant les fichiers de configuration de l'application
     * @var string
     */
    private $buildDirectory = __DIR__.'/.robo/build';

    public function __construct()
    {
        if (!is_file($this->getUserHome().'/.robo')) {
            file_put_contents($this->getUserHome().'/.robo', '<?php return [];');
        }
        if (!is_file($this->configDirectory.'my.config')) {
            file_put_contents($this->configDirectory.'my.config', '<?php return [];');
        }
    }

    public function all()
    {
        $this->config();
        $this->install();
    }

    /**
     * Install project
     */
    public function install()
    {
        $this->loadConfig();
        $this->buildApp(__DIR__);
    }

    private function buildApp()
    {
        $this->loadConfig();
        $this->taskCopyReplaceDir([$this->buildDirectory => __DIR__])
            ->from(array_keys($this->configVariables))
            ->to($this->configVariables)
            ->startDelimiter('$$')
            ->endDelimiter('$$')
            ->dirPermissions(0755)
            ->filePermissions(0644)
            ->run();
    }

    /**
     * Configure the application
     */
    public function config()
    {
        $this->configVariables = $this->taskConfiguration()
            ->initConfig($this->getProperties('config'))
            ->initLocal($this->getProperties('local'))
            ->initSettings($this->getProperties('settings'))
            ->configFilePath($this->configDirectory.'my.config')
            ->force(true)
            ->run()
            ->getData();
    }

    private function loadConfig()
    {
        $this->configVariables = $this->taskConfiguration()
         ->initConfig($this->getProperties('config'))
         ->initLocal($this->getProperties('local'))
         ->initSettings($this->getProperties('settings'))
         ->configFilePath($this->configDirectory.'my.config')
         ->run()
         ->getData();
    }

    /**
     * Retourne les propriétés de configurations
     *
     * @param  string $type
     * @return array
     */
    private function getProperties($type)
    {
        if (!isset($this->properties)) {
            $this->properties = include $this->configDirectory.'properties.php';
        }
        return $this->properties[$type];
    }

    private function getUserHome()
    {
        $home = getenv('HOME');
        if (!empty($home)) {
            // home should never end with a trailing slash.
            $home = rtrim($home, '/');
        } elseif (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'].$_SERVER['HOMEPATH'];
            // If HOMEPATH is a root directory the path can end with a slash. Make sure
            // that doesn't happen.
            $home = rtrim($home, '\\/');
        }
        return empty($home) ? null : $home;
    }
}