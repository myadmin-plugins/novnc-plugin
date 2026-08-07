<?php

declare(strict_types=1);

namespace Detain\MyAdminNovnc\Tests;

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
 *  - **Fingerprint.** Findings are read as objects, not scraped out of rendered failure
 *    text. The failure count must match exactly, every recorded fingerprint must still be
 *    present, and there must be no finding of *any* other severity alongside them. A fifth
 *    broken registration fails the build whether it surfaces as a failure or as a skip.
 *  - **Staleness.** Zero failures means the deferral is stale and the test says so —
 *    including when the assertion stops failing by becoming skipped or not-applicable
 *    rather than passing, which is what deleting `getRequirements()` outright would do.
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

        // Inspect the findings directly rather than running the parent and reading its
        // rendered failure text. Code review found three holes in the text-scraping version,
        // all of which close here:
        //
        //  - A finding that was not a *failure* was invisible. The parent renders failures
        //    only, so a fifth broken registration that came back as a skip -- which is the
        //    default arm for a plugin repo, none of which ship include/ -- kept the bullet
        //    count at four and was swallowed by a green skip claiming "4 known defects".
        //  - Staleness was only detected when the parent returned *normally*. Deleting
        //    getRequirements() outright, the most likely fix here since it registers nothing
        //    but the four bogus entries, made the parent skip, which bypassed the check and
        //    left a dead deferral behind. Zero failures now means stale, however that came
        //    about.
        //  - Rewording the installer's bullet format turned this repo red with a wrong
        //    diagnosis. Nothing here depends on message formatting any more.
        $findings = $inspector->inspect($this->contractSubject());
        $failures = [];
        $others = [];
        foreach ($findings as $finding) {
            if ($finding->isFailure()) {
                $failures[] = $finding->message();
            } else {
                $others[] = $finding->severity().': '.$finding->message();
            }
        }

        $this->assertNotSame(
            [],
            $failures,
            $id.' is listed in '.__TRAIT__.' but reports no failures. The deferral is stale —'
            .' delete the entry. A register that outlives its defect stops being a record and'
            .' starts being a blind spot.'
        );
        $this->assertDeferralStillDescribes($id, $entry, $failures, $others);

        $this->markTestSkipped(
            $id.' fails on '.count($entry['findings']).' known plugin defect(s), deferred to '
            .$entry['issue'].' until '.$entry['until'].'. This is a P-bug in the plugin, not a'
            .' harness defect, and it is recorded rather than fixed here so the harness'
            .' conversion stays reviewable on its own. Findings: '
            .implode(' | ', $entry['findings'])
        );
    }

    /**
     * Fails unless the observed findings are exactly the deferred ones.
     *
     * Both halves matter. Every recorded fingerprint must still be present, so a defect that
     * changes shape is not silently re-covered; and there must be no finding of any severity
     * beyond the recorded ones, so an additional defect cannot ride along under the count.
     * The second half is the one the earlier text-scraping version got wrong.
     *
     * @param string                                                      $id
     * @param array{until:string,issue:string,findings:array<int,string>} $entry
     * @param array<int,string>                                           $failures
     * @param array<int,string>                                           $others non-failure findings
     * @return void
     */
    private function assertDeferralStillDescribes($id, array $entry, array $failures, array $others)
    {
        $report = "\nfailures:\n  ".implode("\n  ", $failures)
            .($others === [] ? '' : "\nother findings:\n  ".implode("\n  ", $others));

        $this->assertCount(
            count($entry['findings']),
            $failures,
            $id.' reported '.count($failures).' failure(s) but '.count($entry['findings'])
            .' are deferred. The deferral covers only the defects it names; something else is'
            .' broken here.'.$report
        );
        $this->assertSame(
            [],
            $others,
            $id.' reported '.count($others).' finding(s) that are not failures alongside the'
            .' deferred ones. A skip or notice is not covered by this deferral and must not be'
            .' hidden by it — the count check alone would not have seen it.'.$report
        );
        foreach ($entry['findings'] as $needle) {
            $matched = false;
            foreach ($failures as $message) {
                if (strpos($message, $needle) !== false) {
                    $matched = true;
                    break;
                }
            }
            $this->assertTrue(
                $matched,
                $id.' no longer reports the deferred finding "'.$needle.'". The defect changed'
                .' shape, so the deferral no longer describes it.'.$report
            );
        }
    }
}
