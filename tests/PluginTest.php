<?php

declare(strict_types=1);

namespace Detain\MyAdminNovnc\Tests;

use Detain\MyAdminNovnc\Plugin;
use MyAdmin\Plugins\Testing\DeferredContractDefects;
use MyAdmin\Plugins\Testing\PluginContractTestCase;

/**
 * Contract assertions for the NoVNC plugin.
 *
 * ---------------------------------------------------------------------------------
 * THE DEFERRED B-10 FAILURE
 * ---------------------------------------------------------------------------------
 * This package registers four requirement paths — `class.Novnc` at `src/Novnc.php`, and
 * `deactivate_kcare` / `deactivate_abuse` / `get_abuse_licenses` at `src/abuse.inc.php` —
 * and neither of those files has ever existed here. They are scaffold copy-paste from
 * `myadmin-abuse-plugin`, they belong to plugin-plan Phase 5 Bucket 1 (the
 * `deactivate_kcare` / `deactivate_abuse` / `get_abuse_licenses` family), and Bucket 1 has
 * to be fixed as one fleet-wide change: `Loader::add_requirement()` is last-write-wins, so
 * fixing one repo only moves which package wins.
 *
 * The four failures are therefore *recorded* rather than fixed here, so the harness
 * conversion stays reviewable on its own. The record is data in this package's
 * `composer.json` under `extra.myadmin-deferred-contract-defects`; the mechanism that
 * enforces it — expiry, exact fingerprints, no additional finding of any severity riding
 * along, and staleness however the failures go away — is
 * {@see \MyAdmin\Plugins\Testing\DeferredContractDefects}, shared by the whole fleet. The
 * deferral is disclosed in the fleet triage matrix's Deferrals section as well as here, and
 * it does **not** turn the matrix cell green: `docs/phase2-triage-matrix.md` still reports
 * B-10 as failing for this package.
 *
 * Delete the `extra` block and the `use` below as part of the Phase 5 fix.
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
    use DeferredContractDefects;

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
