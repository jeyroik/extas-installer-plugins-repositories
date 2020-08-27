<?php
namespace tests\plugins;

use Dotenv\Dotenv;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepositoryDescription;
use extas\components\items\SnuffItem;
use extas\components\packages\entities\EntityRepository;
use extas\components\plugins\construct\PluginInstallConstructDefault;
use extas\components\plugins\init\AfterInitRepositoriesPlugins;
use extas\components\plugins\init\InitPluginsInstaller;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\RepositoryDescription;
use extas\components\repositories\RepositoryDescriptionRepository;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\components\THasMagicClass;
use extas\interfaces\plugins\IPluginInstall;
use extas\interfaces\stages\IStagePluginInstallConstruct;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class AfterInitTest
 *
 * @package tests\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class AfterInitTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffRepositoryDynamic;
    use TSnuffPlugins;
    use THasMagicClass;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->createSnuffDynamicRepositories([]);
        $this->registerSnuffRepos([
            'entityRepository' => EntityRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffDynamicRepositories();
    }

    public function testAfterInitPlugin()
    {
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $plugin = new InitPluginsInstaller([
            InitPluginsInstaller::FIELD__INPUT => $this->getInput(),
            InitPluginsInstaller::FIELD__OUTPUT => $output
        ]);
        $plugin('extas.init.section.plugins_install', [
            [
                IPluginInstall::FIELD__REPOSITORY => 'unknown',
                IPluginInstall::FIELD__NAME => 'snuff item',
                IPluginInstall::FIELD__SECTION => 'snuff_items',
            ]
        ]);
        $outputText = $output->fetch();
        $this->assertStringContainsString(
            'Skip item, cause repository "unknown" is not initialized yet.',
            $outputText,
            'Current output: ' . $outputText
        );

        $this->createWithSnuffRepo('extensionRepository', new Extension([
            Extension::FIELD__CLASS => ExtensionRepositoryDescription::class,
            Extension::FIELD__SUBJECT => '*',
            Extension::FIELD__METHODS => ['unknown']
        ]));

        $repo = new RepositoryDescriptionRepository();
        $repo->create(new RepositoryDescription([
            RepositoryDescription::FIELD__NAME => 'any',
            RepositoryDescription::FIELD__ALIASES => ['unknown'],
            RepositoryDescription::FIELD__PRIMARY_KEY => 'name',
            RepositoryDescription::FIELD__SCOPE => 'extas'
        ]));

        $afterInit = new AfterInitRepositoriesPlugins([
            AfterInitRepositoriesPlugins::FIELD__INPUT => $this->getInput(),
            AfterInitRepositoriesPlugins::FIELD__OUTPUT => $output
        ]);

        $afterInit([
            [
                'name' => 'test',
                AfterInitRepositoriesPlugins::SECTION__NAME => [
                    [
                        IPluginInstall::FIELD__REPOSITORY => "unknown",
                        IPluginInstall::FIELD__NAME => "snuff item",
                        IPluginInstall::FIELD__SECTION => "snuff_items"
                    ]
                ]
            ]
        ]);

        $outputText .= $output->fetch();
        $this->assertStringContainsString(
            'Created install plugin for snuff_items',
            $outputText,
            'Current output: ' . $outputText
        );
        $this->assertStringContainsString(
            'Created uninstall plugin for snuff_items',
            $outputText,
            'Current output: ' . $outputText
        );
    }

    public function testMissedSection()
    {
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);

        $afterInit = new AfterInitRepositoriesPlugins([
            AfterInitRepositoriesPlugins::FIELD__INPUT => $this->getInput(),
            AfterInitRepositoriesPlugins::FIELD__OUTPUT => $output
        ]);

        $afterInit([['name' => 'test']]);

        $outputText = $output->fetch();
        $this->assertStringContainsString(
            'Missed section "' . AfterInitRepositoriesPlugins::SECTION__NAME . '"',
            $outputText,
            'Current output: ' . $outputText
        );
    }
}
