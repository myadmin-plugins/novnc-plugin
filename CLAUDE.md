# MyAdmin NoVNC Plugin

Composer package — provides NoVNC HTML5 VNC connection handling for the MyAdmin control panel framework.

## Commands

```bash
composer install        # install deps
vendor/bin/phpunit      # run tests
```

```bash
vendor/bin/phpunit --verbose tests/  # verbose test run
```

## Architecture

- **Plugin entry**: `src/Plugin.php` · namespace `Detain\MyAdminNovnc` · PSR-4
- **Tests**: `tests/PluginTest.php` · namespace `Detain\MyAdminNovnc\Tests`
- **Autoload**: `Detain\MyAdminNovnc\` → `src/` · dev: `Detain\MyAdminNovnc\Tests\` → `tests/`
- **Event system**: Symfony `GenericEvent` via `symfony/event-dispatcher` ^5|^6|^7
- **CI/CD**: `.github/` (contains workflows for automated testing and deployment) · `.scrutinizer.yml` · `.codeclimate.yml` · `.bettercodehub.yml`
- **IDE settings**: `.idea/` (contains inspectionProfiles, deployment.xml, encodings.xml)

## Plugin Class Pattern

All plugin logic lives in static methods on `Plugin` in `src/Plugin.php`:

```php
namespace Detain\MyAdminNovnc;
use Symfony\Component\EventDispatcher\GenericEvent;

class Plugin {
    public static $name = 'Novnc Plugin';
    public static $type = 'plugin';

    public static function getHooks(): array {
        return [
            'event.name' => [self::class, 'handlerMethod'],
        ];
    }

    public static function getMenu(GenericEvent $event) {
        $menu = $event->getSubject();
        if ($GLOBALS['tf']->ima == 'admin') {
            function_requirements('has_acl');
            if (has_acl('client_billing')) { /* add menu items */ }
        }
    }

    public static function getRequirements(GenericEvent $event) {
        $loader = $event->getSubject();
    }

    public static function getSettings(GenericEvent $event) {
        $settings = $event->getSubject();
    }
}
```

## Conventions

- All public methods on `Plugin` are `static`
- `getHooks()` returns `['event.name' => [self::class, 'method']]` map
- `getRequirements()` calls `$loader->add_requirement()` for each required file
- ACL checks via `has_acl()` after `function_requirements('has_acl')` in `getMenu()`
- Tabs for indentation (see `.scrutinizer.yml` coding style)
- PHP >= 7.4, `ext-soap` required
- LGPL-2.1-only license

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->
