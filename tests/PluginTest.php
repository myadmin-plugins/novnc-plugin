<?php

declare(strict_types=1);

namespace Detain\MyAdminNovnc\Tests;

use Detain\MyAdminNovnc\Plugin;
use MyAdmin\Plugins\Testing\PluginContractTestCase;

/**
 * Contract assertions for the NoVNC plugin.
 *
 * ---------------------------------------------------------------------------------
 * THE B-10 DEFERRAL, NOW RETIRED
 * ---------------------------------------------------------------------------------
 * This package used to register four requirement paths — `class.Novnc` at `src/Novnc.php`,
 * and `deactivate_kcare` / `deactivate_abuse` / `get_abuse_licenses` at `src/abuse.inc.php`
 * — and neither file had ever existed here. They were scaffold copy-paste from
 * `myadmin-abuse-plugin`. Because `Loader::add_requirement()` is last-write-wins, fixing one
 * repo would only have moved which package won, so the failures were *recorded* rather than
 * fixed during the harness conversion, as data in `composer.json` under
 * `extra.myadmin-deferred-contract-defects`.
 *
 * Phase 5 Bucket 1 deleted all 44 of those registrations across 11 packages as one change,
 * so the deferral, its `composer.json` block and the `DeferredContractDefects` trait are all
 * gone from this repo. B-10 now passes on its own terms: every source this plugin registers
 * resolves to a file that exists, because it registers none.
 *
 * ---------------------------------------------------------------------------------
 * WHAT HAPPENED TO THE OLD TESTS
 * ---------------------------------------------------------------------------------
 * The 593-line `PluginTest.php` this replaced contained four tests —
 * `testGetRequirementsRegistersExpectedRequirements`, `testGetRequirementsRegistersPaths`,
 * `testGetRequirementsNovncClassPath` and `testGetRequirementsAbuseFilePaths` — that
 * asserted these four registrations are *present and spelled this way*. They were green for
 * years because they checked the registration table and never the filesystem. They were not
 * coverage; they were a lock on the bug, and a future PR deleting the dead registrations
 * would have had to delete a passing test to do it. They are removed rather than kept, and
 * B-10 now asserts the opposite and stronger property: that every registered source resolves
 * to a file that exists.
 */
class PluginTest extends PluginContractTestCase
{
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
     * Both entries in getHooks() are commented out, so this plugin registers nothing at all
     * -- which is also why its four dead requirement registrations were unreachable, and why
     * deleting them in Phase 5 changed no behaviour here. Pinning the empty table keeps that
     * a deliberate, visible fact rather than something that quietly drifted.
     *
     * @return void
     */
    public function testRegistersItsIdentityAndHooks(): void
    {
        $this->assertSame('plugin', Plugin::$type, 'changing $type silently changes which assertions apply');
        $this->assertSame([], Plugin::getHooks(), 'this plugin is currently expected to register no hooks');
    }
}
