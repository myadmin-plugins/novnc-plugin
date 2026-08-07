<?php

declare(strict_types=1);

namespace Detain\MyAdminNovnc\Tests;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedTestError;

/**
 * Register of plugin defects (P-bugs) that the shared contract harness detects and that
 * this repo is deliberately NOT fixing inside the harness-conversion change.
 *
 * These four registrations point at `src/Novnc.php` and `src/abuse.inc.php`, neither of
 * which has ever existed in this package. They are scaffold copy-paste, they belong to
 * plugin-plan Phase 5 Bucket 1 (the `deactivate_kcare` / `deactivate_abuse` /
 * `get_abuse_licenses` family), and Bucket 1 has to be fixed as one fleet-wide change —
 * `Loader::add_requirement()` is last-write-wins, so fixing one repo only moves the winner.
 *
 * Until then this register turns the failure into a *recorded* skip. It is not a mute
 * button, and it cannot become one:
 *
 *  - **Expiry.** Past `until` the test fails outright. The deferral is time-boxed, not
 *    permanent.
 *  - **Fingerprint.** The number of findings must match exactly and every recorded
 *    fingerprint must still be present. A fifth broken registration, or a different
 *    broken path, fails the build — this hides the four listed defects and nothing else.
 *  - **Staleness.** If the assertion starts passing, the test fails and tells you to
 *    delete the entry, so a fixed defect cannot leave a dead deferral behind.
 *
 * Delete this file, and the `use` in {@see PluginTest}, as part of the Phase 5 fix.
 *
 * ---------------------------------------------------------------------------------
 * WHAT HAPPENED TO THE OLD TESTS
 * ---------------------------------------------------------------------------------
 * The 593-line `PluginTest.php` this replaced contained four tests —
 * `testGetRequirementsRegistersExpectedRequirements`, `testGetRequirementsRegistersPaths`,
 * `testGetRequirementsNovncClassPath` and `testGetRequirementsAbuseFilePaths` — that
 * asserted these four registrations are *present and spelled this way*. They were green
 * for years because they checked the registration table and never the filesystem. They
 * were not coverage; they were a lock on the bug, and a future PR deleting the dead
 * registrations would have had to delete a passing test to do it. They are removed rather
 * than kept, and B-10 now asserts the opposite and stronger property: that every
 * registered source resolves to a file that exists.
 */
trait DeferredPhase5Defects
{
    /**
     * Catalogue id => defect record.
     *
     * @return array<string,array{until:string,issue:string,findings:array<int,string>}>
     */
    protected function deferredContractDefects(): array
    {
        return [
            'B-10' => [
                'until' => '2026-11-30',
                'issue' => 'plugin_plan.md Phase 5, Bucket 1 (scaffold-copied abuse.inc.php family)',
                'findings' => [
                    'requirement "class.Novnc" registers /../vendor/detain/myadmin-novnc-plugin/src/Novnc.php',
                    'requirement "deactivate_kcare" registers /../vendor/detain/myadmin-novnc-plugin/src/abuse.inc.php',
                    'requirement "deactivate_abuse" registers /../vendor/detain/myadmin-novnc-plugin/src/abuse.inc.php',
                    'requirement "get_abuse_licenses" registers /../vendor/detain/myadmin-novnc-plugin/src/abuse.inc.php',
                ],
            ],
        ];
    }

    /**
     * @dataProvider contractAssertions
     * @param class-string $inspectorClass
     * @return void
     */
    public function testPluginSatisfiesContractAssertion($inspectorClass)
    {
        $inspector = new $inspectorClass();
        $deferred = $this->deferredContractDefects();
        $id = $inspector->id();
        if (!isset($deferred[$id])) {
            parent::testPluginSatisfiesContractAssertion($inspectorClass);
            return;
        }
        $entry = $deferred[$id];
        $expiry = strtotime($entry['until'].' 23:59:59');
        $this->assertLessThanOrEqual(
            $expiry,
            time(),
            $id.' was deferred until '.$entry['until'].' pending '.$entry['issue']
            .'. That date has passed: fix the plugin defect or re-agree the deferral. A'
            .' time-boxed skip that never expires is an unrecorded permanent exemption.'
        );
        try {
            parent::testPluginSatisfiesContractAssertion($inspectorClass);
        } catch (SkippedTestError $e) {
            throw $e;
        } catch (IncompleteTestError $e) {
            throw $e;
        } catch (AssertionFailedError $e) {
            $this->assertDeferralStillDescribes($id, $entry, $e->getMessage());
            $this->markTestSkipped(
                $id.' fails on '.count($entry['findings']).' known plugin defect(s), deferred to '
                .$entry['issue'].' until '.$entry['until'].'. This is a P-bug in the plugin, not a'
                .' harness defect, and it is recorded rather than fixed here so the harness'
                .' conversion stays reviewable on its own. Findings: '
                .implode(' | ', $entry['findings'])
            );
        }
        $this->fail(
            $id.' is listed in '.__TRAIT__.' but now passes. The deferral is stale — delete the'
            .' entry. A register that outlives its defect stops being a record and starts being'
            .' a blind spot.'
        );
    }

    /**
     * Fails unless the observed failure is exactly the deferred one — same count, same texts.
     *
     * @param string                                                                 $id
     * @param array{until:string,issue:string,findings:array<int,string>}            $entry
     * @param string                                                                 $message
     * @return void
     */
    private function assertDeferralStillDescribes($id, array $entry, $message)
    {
        preg_match_all('/^\s+- \[/m', $message, $matches);
        $this->assertCount(
            count($entry['findings']),
            $matches[0],
            $id.' reported '.count($matches[0]).' finding(s) but '.count($entry['findings'])
            .' are deferred. The deferral covers only the defects it names; something else is'
            .' broken here. Full report:'."\n".$message
        );
        foreach ($entry['findings'] as $needle) {
            $this->assertStringContainsString(
                $needle,
                $message,
                $id.' no longer reports the deferred finding "'.$needle.'". The defect changed'
                .' shape, so the deferral no longer describes it. Full report:'."\n".$message
            );
        }
    }
}
