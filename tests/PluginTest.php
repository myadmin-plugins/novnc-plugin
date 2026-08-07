<?php

declare(strict_types=1);

namespace Detain\MyAdminNovnc\Tests;

use Detain\MyAdminNovnc\Plugin;
use MyAdmin\Plugins\Testing\PluginContractTestCase;

/** Contract assertions for the NoVNC plugin. */
class PluginTest extends PluginContractTestCase
{
    use DeferredPhase5Defects;

    /** @return string */
    protected function pluginClass()
    {
        return Plugin::class;
    }

    /**
     * Pins this plugin's identity and its (empty) hook table.
     *
     * The shared harness deliberately cannot do this: every catalogue assertion is
     * conditional on the registration existing, so getHooks() can change freely without
     * the suite noticing. $type is read by the harness but never pinned.
     *
     * Both entries in getHooks() are commented out, so this plugin registers nothing and
     * its four requirement registrations are unreachable. Pinning the empty table keeps
     * that a deliberate, visible fact rather than something that quietly drifted -- and
     * makes the Phase 5 fix, whichever direction it goes, show up here as a failure.
     *
     * @return void
     */
    public function testRegistersItsIdentityAndHooks(): void
    {
        $this->assertSame('plugin', Plugin::$type, 'changing $type silently changes which assertions apply');
        $this->assertSame([], Plugin::getHooks(), 'this plugin is currently expected to register no hooks');
    }
}
