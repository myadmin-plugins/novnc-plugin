---
name: plugin-test
description: Writes PHPUnit 9.x tests in `tests/PluginTest.php` following the existing test class structure under namespace `Detain\MyAdminNovnc\Tests`. Use when user says 'add test', 'write test', 'test the plugin', or adds new methods to `src/Plugin.php`. Do NOT use for integration tests or tests outside the `tests/` directory. For a plugin's contract or behavioral tests (tests/ContractTest.php, the shared harness, composer myadmin:scaffold-tests) use the plugin-contract-tests skill instead — this skill's reflection-only guidance predates that harness.
---
<!-- myadmin-contract-harness-notice -->
> ### ⚠️ Read this before the rest of the file
>
> This package is on the **shared plugin contract harness**. Parts of the guidance below
> predate it and are now wrong in one specific way:
>
> **Any instruction here that a plugin's `getHooks()` / `getSettings()` / `getActivate()` /
> `getDeactivate()` / `getQueue()` must not be *called* — that only its existence, visibility
> or parameter count may be checked through `ReflectionClass` — no longer applies.** That rule
> existed because those methods reference bare constants (`PRORATE_BILLING` and friends) that
> only a live MyAdmin request defines, so calling them from a test used to fatal. The harness
> defines them first. It then executes the handlers for real, in a process of its own.
>
> A reflection-only assertion passes whether or not the thing works: `getActivate()` can exist,
> be public, be static, take one argument, and still fatal the moment it runs. Three real
> production bugs in this fleet were sitting behind assertions of exactly that shape.
>
> **Use the `plugin-contract-tests` skill** for anything touching `tests/ContractTest.php`,
> the contract inspectors, or `composer myadmin:scaffold-tests`.
>
> **Everything else in this file is still accurate and still applies** — this package's own
> classes, its API wrappers, its fixtures, its bootstrap, and the reasons certain classes must
> not be constructed. Nothing below has been removed.

# plugin-test

## Critical

- ALL tests live in `tests/PluginTest.php` — never create new test files
- Namespace MUST be `Detain\MyAdminNovnc\Tests`; class MUST be `PluginTest extends TestCase`
- File MUST start with `declare(strict_types=1);`
- Use tabs for indentation (project standard — see `.scrutinizer.yml`)
- For event-handler tests, use an **anonymous class** as the subject — never mock vendor or global classes
- Never test `getMenu` by calling it directly — it accesses `$GLOBALS['tf']` which is unavailable in unit tests; test its signature via `ReflectionClass` instead

## Instructions

1. **Read `src/Plugin.php`** to identify the method or property being tested. Note its name, parameters, return type, and whether it is `static`.
   - Verify the method exists before writing any test.

2. **Open `tests/PluginTest.php`** and append new test methods inside `PluginTest`. Never replace existing tests.
   - Required imports (already present; keep them): `use Detain\MyAdminNovnc\Plugin;`, `use PHPUnit\Framework\TestCase;`, `use ReflectionClass;`, `use Symfony\Component\EventDispatcher\GenericEvent;`

3. **Write a signature/reflection test** for any new static method:
   ```php
   public function testMyMethodMethodSignature(): void
   {
       $reflection = new ReflectionClass(Plugin::class);
       $method = $reflection->getMethod('myMethod');
       $this->assertTrue($method->isStatic(), 'myMethod should be static');
       $this->assertTrue($method->isPublic(), 'myMethod should be public');
       $this->assertCount(1, $method->getParameters(), 'myMethod should accept exactly one parameter');
       $param = $method->getParameters()[0];
       $this->assertSame('event', $param->getName());
   }
   ```

4. **Write a parameter-type test** if the method accepts `GenericEvent`:
   ```php
   public function testMyMethodParameterType(): void
   {
       $reflection = new ReflectionClass(Plugin::class);
       $method = $reflection->getMethod('myMethod');
       $param = $method->getParameters()[0];
       $this->assertTrue($param->hasType());
       $this->assertSame(GenericEvent::class, $param->getType()->getName());
   }
   ```

5. **Write a behavioral test** using an anonymous class as the event subject (mirrors `testGetRequirementsRegistersExpectedRequirements`):
   ```php
   public function testMyMethodDoesSomething(): void
   {
       $subject = new class {
           public array $calls = [];
           public function someMethod(string $a, string $b): void {
               $this->calls[] = ['a' => $a, 'b' => $b];
           }
       };
       $event = new GenericEvent($subject);
       Plugin::myMethod($event);
       $this->assertCount(1, $subject->calls);
       $this->assertSame('expected', $subject->calls[0]['a']);
   }
   ```
   - This step uses the anonymous class pattern established in `testGetRequirementsRegistersExpectedRequirements` (line 279).

6. **Update `testPublicMethodCount`** if a new public method was added: change the `assertCount` value to match the new total.
   - Also add the method name to the `$expectedMethods` array in `testExpectedMethodsExist`.

7. **Run tests** to verify: `vendor/bin/phpunit`
   - All prior tests must still pass; no regressions.

## Examples

**User says:** "Add a test for a new `getStatus` static method that calls `$loader->set_status(string)` on the event subject."

**Actions:**
1. Read `src/Plugin.php` — confirm `getStatus(GenericEvent $event)` exists.
2. Append to `tests/PluginTest.php`:

```php
/**
 * Test that the getStatus method exists and is static.
 *
 * @return void
 */
public function testGetStatusMethodSignature(): void
{
	$reflection = new ReflectionClass(Plugin::class);
	$method = $reflection->getMethod('getStatus');
	$this->assertTrue($method->isStatic(), 'getStatus should be static');
	$this->assertTrue($method->isPublic(), 'getStatus should be public');
	$this->assertCount(1, $method->getParameters(), 'getStatus should accept exactly one parameter');
	$param = $method->getParameters()[0];
	$this->assertSame('event', $param->getName());
}

/**
 * Test that getStatus calls set_status on the loader subject.
 *
 * @return void
 */
public function testGetStatusSetsStatus(): void
{
	$subject = new class {
		public array $statuses = [];
		public function set_status(string $status): void {
			$this->statuses[] = $status;
		}
	};
	$event = new GenericEvent($subject);
	Plugin::getStatus($event);
	$this->assertNotEmpty($subject->statuses);
}
```
3. Update `testPublicMethodCount` assertCount from `5` to `6` and add `'getStatus'` to `testExpectedMethodsExist`.
4. Run `vendor/bin/phpunit` — all tests pass.

**Result:** Two new tests in `PluginTest`, existing tests untouched, count assertions updated.

## Common Issues

- **`Error: Call to undefined method ... getSubject()`** — You passed a plain object to `Plugin::getMenu()` directly. `getMenu` reads `$GLOBALS['tf']` which is not set in tests. Test the signature via `ReflectionClass` only; do not call `getMenu` directly.

- **`ReflectionException: Method myMethod does not exist`** — The method name in the test doesn't match the actual method in `src/Plugin.php`. Run `grep 'public static function' src/Plugin.php` to get exact names.

- **`Failed asserting that 5 matches expected 6`** in `testPublicMethodCount` — You added a method to `Plugin` but forgot to update the `assertCount` in `testPublicMethodCount` (line ~418). Increment it by 1 and add the method to `testExpectedMethodsExist`.

- **`ParseError: syntax error`** after appending — Ensure the new methods are placed *before* the closing `}` of the class. Check indentation uses tabs, not spaces.

- **`Class 'Detain\MyAdminNovnc\Plugin' not found`** — Run `composer install` first to generate the autoloader, then `vendor/bin/phpunit`.