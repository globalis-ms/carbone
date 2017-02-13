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
     * Directory containing all configuration variables
     * @var string
     */
    private $configDirectory = __DIR__.'/.robo/config/';

    /**
     * Directory containing all build files
     * @var string
     */
    private $buildDirectory = __DIR__.'/.robo/build';

    public function __construct()
    {
        // Create robo user config file if necessary
        if (!is_file($this->getUserHome().'/.robo')) {
            file_put_contents($this->getUserHome().'/.robo', '<?php return [];');
        }
        // Create robo project config file if necessary
        if (!is_file($this->configDirectory.'my.config')) {
            file_put_contents($this->configDirectory.'my.config', '<?php return [];');
        }
    }

    /**
     * Configure application and replace configuration files
     */
    public function all()
    {
        $this->config();
        $this->install();
    }

    /**
     * Configure application
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

    /**
     * Replace configuration files
     */
    public function install()
    {
        $this->loadConfig();
        $this->buildApp(__DIR__);
    }

    /**
     * Start a new feature
     *
     * @param  string $name The feature name
     */
    public function featureStart($name)
    {
        $this->loadConfig();
        return $this->taskFeatureStart($name, $this->configVariables['BIN_GIT_PATH'])->run();
    }

    /**
     * Finish a feature
     *
     * @param  string $name The feature name
     */
    public function featureFinish($name)
    {
        $this->loadConfig();
        return $this->taskFeatureFinish($name, $this->configVariables['BIN_GIT_PATH'])->run();
    }

    /**
     * Start a new hotfix
     *
     * @option string $semversion Version number
     * @option string $type    Hotfix type (path, minor)
     */
    public function hotfixStart($opts = ['semversion' => null, 'type' => 'patch'])
    {
        $this->loadConfig();
        if (empty($opts['semversion'])) {
            $version = $this->getVersion()
                ->increment($opts['type']);
        } else {
            $version = $opts['semversion'];
        }
        $this->loadConfig();
        return $this->taskHotfixStart((string)$version, $this->configVariables['BIN_GIT_PATH'])->run();
    }

    /**
     * Finish a hotfix
     *
     * @option string $semversion Version number
     * @option string $type    Hotfix type (path, minor)
     */
    public function hotfixFinish($opts = ['semversion' => null, 'type' => 'patch'])
    {
        $this->loadConfig();
        if (empty($opts['semversion'])) {
            $version = $this->getVersion()
                ->increment($opts['type']);
        } else {
            $version = $opts['semversion'];
        }
        return $this->taskHotfixFinish((string)$version, $this->configVariables['BIN_GIT_PATH'])->run();
    }

    /**
     * Start a new release
     *
     * @option string $semversion Version number
     * @option string $type    Release type (minor, major)
     */
    public function releaseStart($opts = ['semversion' => null, 'type' => 'minor'])
    {
        $this->loadConfig();
        if (empty($opts['semversion'])) {
            $version = $this->getVersion()
                ->increment($opts['type']);
        } else {
            $version = $opts['semversion'];
        }
        return $this->taskReleaseStart((string)$version, $this->configVariables['BIN_GIT_PATH'])->run();
    }

    /**
     * Finish a release
     *
     * @option string $semversion Version number
     * @option string $type    Release type (minor, major)
     */
    public function releaseFinish($opts = ['semversion' => null, 'type' => 'minor'])
    {
        $this->loadConfig();
        if (empty($opts['semversion'])) {
            $version = $this->getVersion()
                ->increment($opts['type']);
        } else {
            $version = $opts['semversion'];
        }
        return $this->taskReleaseFinish((string)$version, $this->configVariables['BIN_GIT_PATH'])->run();
    }

    /**
     * Deploy the application on a remote server
     *
     * @param  string $env
     */
    public function deploy($env)
    {
        $this->loadConfig();

        $this->io()->title('Deploy to '.$env);

        // Load remote config (and ask everything needed)
        $remoteConfig = $this->loadRemoteConfig($env);
        $remoteConfig['REMOTE_PATH'] = $this->trailingslashit($remoteConfig['REMOTE_PATH']);

        $this->io()->section('Preparation :');
        
        // Get the temporary work directory
        $collection = $this->collectionBuilder();
        $workDir = $this->trailingslashit($collection->tmpDir());
        // Create the temporary work directory
        $this->taskExec('mkdir '.$workDir)->run();
        // Tar everything into the work directory (faster for copying), then untar it
        $this->taskExec('tar cf '.$workDir.'tmp.tar . ; cd '.$workDir.' ; tar xf tmp.tar && rm tmp.tar')->run();
        $this->io()->text('');

        $this->io()->section('Build :');

        // Build the remote application
        $this->buildRemoteApp($workDir, $remoteConfig);
        $this->io()->text('');

        $this->io()->section('Dry run rsync :');

        // Dry Run rsync
        $this->rsync($workDir, $remoteConfig['REMOTE_USER'], $remoteConfig['REMOTE_HOST'], $remoteConfig['REMOTE_PATH'], true);
        
        // Ask confirmation
        if ($this->io()->confirm('Do you want to run rsync ?', false)) {
            // Run rsync
            $this->io()->section('Run rsync :');
            $this->rsync($workDir, $remoteConfig['REMOTE_USER'], $remoteConfig['REMOTE_HOST'], $remoteConfig['REMOTE_PATH'], false);
            $this->io()->text('');
        }

        $this->io()->section('Clean :');

        // Delete the temporary work directory
        $this->taskDeleteDir($workDir)->run();
        $this->io()->text('');
    }

    /**
     * Rsync project directory into remote server
     *
     * @param  string $workDir Source directory
     * @param  string $remoteUser Remote username
     * @param  string $remoteHost Remote host
     * @param  string $remotePath Remote path
     * @param  boolean $dryRun Run rsync as a dry run
     *
     * @return boolean Command Result
     */
    private function rsync($workDir, $remoteUser, $remoteHost, $remotePath, $dryRun = true) {
        $cmd = $this->taskRsync()
            ->fromPath($workDir)
            ->toHost($remoteHost)
            ->toUser($remoteUser)
            ->toPath($remotePath)
            ->verbose()
            ->recursive()
            ->checksum()
            ->compress()
            ->itemizeChanges()
            ->excludeVcs()
            ->progress()
            ->option('copy-links')
            ->option('perms')
            ->option('chmod', 'Du=rwx,Dgo=rx,Fu=rw,Fgo=r')
            ->stats();

        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '.rsyncignore')) {
            $cmd->excludeFrom(__DIR__ . DIRECTORY_SEPARATOR . '.rsyncignore');
        }

        if ($dryRun) {
            $cmd->dryRun();
        }
        return $cmd->run();
    }

    /**
     * Return current version
     *
     * @return SemanticVersion
     */
    private function getVersion()
    {
        // Get version from tag
        $cmd = new Command($this->configVariables['BIN_GIT_PATH']);
        $cmd = $cmd->arg('tag')
            ->execute();
        $output = explode(PHP_EOL, trim($cmd->getOutput()));
        $currentVersion = '0.0.0';
        foreach ($output as $tag) {
            if (preg_match(SemanticVersion::REGEX, $tag)) {
                if (version_compare($currentVersion, $tag, '<')) {
                    $currentVersion = $tag;
                }
            }
        }
        return new SemanticVersion($currentVersion);
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

    private function buildRemoteApp($dir, $config)
    {
        $this->taskCopyReplaceDir([$this->buildDirectory => $dir])
            ->from(array_keys($config))
            ->to($config)
            ->startDelimiter('$$')
            ->endDelimiter('$$')
            ->dirPermissions(0755)
            ->filePermissions(0644)
            ->run();
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

        if (!isset($this->configVariables['BIN_GIT_PATH'])) {
            $this->configVariables['BIN_GIT_PATH'] = 'git';
        }
    }

    private function loadRemoteConfig($remote)
    {
        return $this->taskConfiguration()
         ->initConfig(array_merge($this->getProperties('config'), $this->getProperties('remote')))
         ->initLocal($this->getProperties('local'))
         ->initSettings($this->getProperties('settings'))
         ->configFilePath($this->configDirectory.$remote.'.config')
         ->force(true)
         ->run()
         ->getData();
    }

    /**
     * Return configuration properties
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

    /**
     * Return user home path
     *
     * @return string
     */
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

    private function trailingslashit($string) {
        return $this->untrailingslashit($string) . '/';
    }

    private function untrailingslashit($string) {
        return rtrim($string, '/\\');
    }
}