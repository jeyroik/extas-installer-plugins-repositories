<?php
namespace extas\components\plugins\init;

use extas\components\extensions\ExtensionRepository;
use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\interfaces\stages\IStageAfterRepositoriesInit;

/**
 * Class AfterInitRepositoriesPlugins
 *
 * @package extas\components\plugins\init
 * @author jeyroik <jeyroik@gmail.com>
 */
class AfterInitRepositoriesPlugins extends Plugin implements IStageAfterRepositoriesInit
{
    public const SECTION__NAME = 'plugins_install';

    use THasIO;

    /**
     * @param array $packages
     */
    public function __invoke(array $packages): void
    {
        foreach ($packages as $package) {
            $this->createPlugins($package);
        }
    }

    /**
     * @param array $package
     * @return array
     */
    protected function createPlugins(array $package): array
    {
        if (!isset($package[static::SECTION__NAME])) {
            $this->writeLn(['Missed section "' . static::SECTION__NAME . '"']);
            return [];
        }

        $initPlugin = new InitPluginsInstaller($this->getIO());
        $initPlugin(static::SECTION__NAME, $package[static::SECTION__NAME]);

        return $package;
    }
}
