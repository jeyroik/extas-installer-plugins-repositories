<?php
namespace tests\repositories;

use Dotenv\Dotenv;
use extas\components\plugins\construct\PluginConstructDynamicRepo;
use extas\components\plugins\Plugin;
use extas\interfaces\IHasClass;
use extas\interfaces\IHasName;
use extas\interfaces\IHasRepository;
use extas\interfaces\IHasSection;
use extas\interfaces\IHasUid;
use PHPUnit\Framework\TestCase;

/**
 * Class PluginConstructTest
 *
 * @package tests\repositories
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginConstructTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testValid()
    {
        $plugin = new PluginConstructDynamicRepo([
            PluginConstructDynamicRepo::FIELD__SECTION => 'tests',
            PluginConstructDynamicRepo::FIELD__PARAMS => [
                'class' => 'class',
                'pk' => 'name'
            ],
            PluginConstructDynamicRepo::FIELD__ITEM => [
                'name' => 'test',
                'repository' => 'repo'
            ]
        ]);

        $stab = new Plugin();
        $result = $plugin($stab, 'install');

        $this->assertEquals(
            'extas\\components\\plugins\\install\\InstallPluginsInstaller',
            $result->getClass()
        );
        $this->assertEquals(
            'extas.install.section.tests',
            $result->getStage()
        );
        $this->assertEquals(
            [
                IHasRepository::FIELD__REPOSITORY => 'repo',
                IHasUid::FIELD__UID => 'name',
                IHasSection::FIELD__SECTION => 'tests',
                IHasName::FIELD__NAME => 'test',
                IHasClass::FIELD__CLASS => 'class'
            ],
            $result->getParametersValues(),
            'Current: ' . print_r($result->getParametersValues(), true)
        );
    }

    public function testSkip()
    {
        $plugin = new PluginConstructDynamicRepo([
            PluginConstructDynamicRepo::FIELD__SECTION => 'tests',
            PluginConstructDynamicRepo::FIELD__PARAMS => [
                'class' => 'class',
                'pk' => 'name'
            ],
            PluginConstructDynamicRepo::FIELD__ITEM => [
                'name' => 'test',
                'repository' => 'repo'
            ]
        ]);

        $stab = new Plugin([
            Plugin::FIELD__CLASS => 'ok',
            Plugin::FIELD__STAGE => 'ok',
            Plugin::FIELD__PARAMETERS => []
        ]);
        $result = $plugin($stab, 'install');

        $this->assertEquals('ok', $result->getClass());
        $this->assertEquals('ok', $result->getStage());
        $this->assertEquals([], $plugin->getParametersValues());
    }
}
