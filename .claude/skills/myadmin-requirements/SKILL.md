---
name: myadmin-requirements
description: Adds file/class requirements via getRequirements() in src/Plugin.php using $loader->add_requirement(). Use when user says 'add requirement', 'load file', 'register class', or needs to expose a new src file to the MyAdmin loader. Do NOT use for Composer dependencies — those go in composer.json.
---
# myadmin-requirements

## Critical

- **Never** add Composer packages here — use `composer.json` for that.
- All paths must start with `/../vendor/detain/myadmin-novnc-plugin/src/` (leading `/../` is required by the MyAdmin loader).
- Class file alias format: `class.ClassName`. Function/procedure aliases use the function name directly (e.g. `deactivate_abuse`).
- Multiple functions in the same file each get their own `add_requirement()` call with identical paths.
- Use tabs for indentation — not spaces.

## Instructions

1. **Identify the alias and file path.**
   - For a class: alias = `class.ClassName`, path = the full `/../vendor/detain/myadmin-novnc-plugin/src/` path to the class source file.
   - For a function/include: alias = the function name (e.g. `deactivate_abuse`), path = the source file containing it.
   - Verify the target file exists in `src/` before proceeding:
     ```bash
     ls src/
     ```

2. **Open `src/Plugin.php` and locate `getRequirements()`** (currently at line 53).
   The method body always starts with:
   ```php
   $loader = $event->getSubject();
   ```
   Add the new `add_requirement()` call after the existing calls, before the closing `}`.

3. **Add the line using this exact pattern:**
   ```php
   $loader->add_requirement('alias', '/../vendor/detain/myadmin-novnc-plugin/src/SourceFile.php');
   ```
   If multiple functions live in the same file, add one line per alias — all pointing to the same path.

4. **Update the test** in `tests/PluginTest.php`:
   - Increment the count in `testGetRequirementsRegistersExpectedRequirements()` (`assertCount(N, ...)`).
   - Add `assertContains('new_alias', $names)` for the new alias.
   - If the file is new, add a path assertion verifying it `assertStringContainsString('NewFile.php', $path)`.

5. **Run tests to verify:**
   ```bash
   vendor/bin/phpunit
   ```
   All tests must pass before committing.

## Examples

**User says:** "Add a requirement for the `open_novnc_console` function"

**Actions taken:**

1. Confirm the source file exists in `src/` by running `ls src/`.
2. In `src/Plugin.php`, append inside `getRequirements()`:
   ```php
   $loader->add_requirement('open_novnc_console', '/../vendor/detain/myadmin-novnc-plugin/src/NovncConsole.php');
   ```
3. In `tests/PluginTest.php`, update `testGetRequirementsRegistersExpectedRequirements()`:
   ```php
   $this->assertCount(5, $loader->requirements, 'Should register exactly 5 requirements');
   // ...
   $this->assertContains('open_novnc_console', $names);
   ```
4. Run `vendor/bin/phpunit` — all tests green.

**Result:** `getRequirements()` now registers `open_novnc_console` and the loader can lazy-load it on demand.

## Common Issues

- **Test fails with `assertCount` mismatch** (e.g. `Failed asserting that 4 matches expected 5`): You added the requirement in `src/Plugin.php` but forgot to update the count in `testGetRequirementsRegistersExpectedRequirements()` — increment `assertCount(N, ...)` to match.
- **Loader silently ignores the requirement at runtime**: The path does not start with `/../`. Ensure it is `'/../vendor/detain/...'` not `'/vendor/detain/...'`.
- **`vendor/bin/phpunit` reports class not found**: The `src/` file does not exist yet. Create it before adding the requirement, or the loader will error when the alias is triggered.
- **Two functions registered from the same file but only one works**: Each function alias needs its own `add_requirement()` line — the loader maps aliases to files one-to-one. Verify both lines are present.
