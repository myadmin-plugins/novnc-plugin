---
name: plugin-hook
description: Adds a new event hook to the MyAdmin NoVNC plugin system. Registers the hook in `getHooks()` and implements the corresponding static handler method on `Plugin` in `src/Plugin.php`. Use when user says 'add hook', 'register event', 'handle event', or needs to respond to a new MyAdmin event. Do NOT use for modifying `getMenu()` or `getRequirements()` — those have dedicated patterns.
---
# plugin-hook

## Critical

- All handler methods MUST be `public static` — never instance methods.
- The `$event` parameter MUST be typed as `\Symfony\Component\EventDispatcher\GenericEvent`.
- Use `__CLASS__` (not `self::class`) as the callable class reference in `getHooks()` — matches existing code.
- Use tabs for indentation throughout `src/Plugin.php`.
- Never remove or alter existing hooks in `getHooks()` — only add new entries.

## Instructions

1. **Identify the event name and handler method name.**
   Confirm the MyAdmin event string (e.g., `'system.settings'`, `'ui.menu'`) and choose a `camelCase` method name for the handler (e.g., `handleVncConnect`). Verify no method of that name already exists in `src/Plugin.php` before proceeding.

2. **Register the hook in `getHooks()`.**
   Open `src/Plugin.php` and add an entry to the returned array:
   ```php
   public static function getHooks()
   {
       return [
           'your.event.name' => [__CLASS__, 'yourHandlerMethod'],
       ];
   }
   ```
   If active hooks are currently commented out (e.g., `//`), add your entry as a live line — do not uncomment unrelated hooks.

3. **Implement the handler method.**
   Add the static method immediately after `getSettings()`, following this exact signature and doc-block pattern from the existing handlers:
   ```php
   /**
    * @param \Symfony\Component\EventDispatcher\GenericEvent $event
    */
   public static function yourHandlerMethod(GenericEvent $event)
   {
       $subject = $event->getSubject();
       // handler logic here
   }
   ```
   Use `$event->getSubject()` to get the payload object. No return value is needed (handlers are void).

4. **Add a PHPUnit test for the new hook.**
   Open `tests/PluginTest.php` and add two tests:
   - Assert `getHooks()` contains the new event key pointing to `[Plugin::class, 'yourHandlerMethod']`.
   - Assert the handler method is public and static via `ReflectionClass`.
   Follow the doc-block style already in `PluginTest.php`.

5. **Run tests to verify.**
   ```bash
   vendor/bin/phpunit
   ```
   All existing tests must still pass. The new tests must pass too.

## Examples

**User says:** "Add a hook for the `system.novnc_connect` event that logs the connection subject."

**Actions taken:**

`getHooks()` updated:
```php
public static function getHooks()
{
	return [
		'system.novnc_connect' => [__CLASS__, 'handleNovncConnect'],
	];
}
```

New method added after `getSettings()`:
```php
/**
 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
 */
public static function handleNovncConnect(GenericEvent $event)
{
	$subject = $event->getSubject();
	// connection handling logic
}
```

Test added to `tests/PluginTest.php`:
```php
public function testGetHooksContainsNovncConnect(): void
{
	$hooks = Plugin::getHooks();
	$this->assertArrayHasKey('system.novnc_connect', $hooks);
	$this->assertSame([Plugin::class, 'handleNovncConnect'], $hooks['system.novnc_connect']);
}

public function testHandleNovncConnectIsStatic(): void
{
	$reflection = new ReflectionClass(Plugin::class);
	$method = $reflection->getMethod('handleNovncConnect');
	$this->assertTrue($method->isStatic());
	$this->assertTrue($method->isPublic());
}
```

**Result:** `vendor/bin/phpunit` passes with all tests green.

## Common Issues

- **`Call to undefined method` at runtime:** The method name in `getHooks()` must exactly match the implemented method name — PHP is case-sensitive for string-based callables.
- **`testPublicMethodCount` fails (expected 5, got 6):** The existing test in `PluginTest.php:410` asserts exactly 5 own public methods. Update that assertion to reflect the new count after adding your handler.
- **`GenericEvent` not found / class not imported:** Confirm `use Symfony\Component\EventDispatcher\GenericEvent;` is at the top of `src/Plugin.php` — it is already present in the existing file.
- **Indentation inconsistency causes CS failures:** `src/Plugin.php` uses tabs. If your editor inserts spaces, the `.scrutinizer.yml` coding-style check will fail. Run `make php-cs-fixer` (from the parent MyAdmin project) or manually verify tabs are used.
- **Hook never fires:** Confirm the event string matches exactly what the MyAdmin framework dispatches — check `include/config/hooks.json` in the parent project for the authoritative event name list.