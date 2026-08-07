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
}
