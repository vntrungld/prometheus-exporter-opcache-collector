# Laravel 13 Support Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add official Laravel 13 support to the OPcache collector package while keeping Laravel 6–12 support intact.

**Architecture:** This is a dependency-and-CI change. Widen Composer constraints, add a Laravel 13 CI matrix block, update the README compatibility table, then verify the test suite passes against the Laravel 13 dependency set. No `src/` changes are expected because Laravel 13 is a minimal-breaking-change release and the collectors only use PHP's `opcache_get_status()` and `illuminate/support`.

**Tech Stack:** PHP 8.3–8.5, Laravel 13 (`illuminate/support`), Orchestra Testbench 11, PHPUnit 12, GitHub Actions, Composer.

## Global Constraints

- Parent package constraint: `vntrungld/prometheus-exporter` must be `^1.1|^2.0` (Laravel 13 support shipped as parent major 2.x).
- Framework constraint: `illuminate/support` must include `^13.0`, keeping all of `^6.0|^7.0|^8.0|^9.0|^10.0|^11.0|^12.0`.
- PHP floor in `composer.json` stays `^7.2|^8.0` — do NOT change it (already covers 8.3–8.5).
- Dev tooling: add `orchestra/testbench ^11.0` and `phpunit/phpunit ^12.0`; keep existing constraints.
- Laravel 13 CI: exactly two rows — PHP `8.3` and `8.4`, testbench `11.*`, phpunit `12.*`.
- Keep ALL existing legacy matrix rows (Laravel 6–12) and README rows unchanged.

---

### Task 1: Widen Composer constraints for Laravel 13

**Files:**
- Modify: `composer.json:14-22`

**Interfaces:**
- Consumes: nothing.
- Produces: a `composer.json` whose `require`/`require-dev` allow Laravel 13, testbench 11, phpunit 12, and parent `^2.0`. Tasks 2 and 4 rely on these constraints resolving.

- [ ] **Step 1: Edit the `require` block**

In `composer.json`, change the `require` block from:

```json
    "require": {
        "php": "^7.2|^8.0",
        "illuminate/support": "^6.0|^7.0|^8.0|^9.0|^10.0|^11.0|^12.0",
        "vntrungld/prometheus-exporter": "^1.1"
    },
```

to:

```json
    "require": {
        "php": "^7.2|^8.0",
        "illuminate/support": "^6.0|^7.0|^8.0|^9.0|^10.0|^11.0|^12.0|^13.0",
        "vntrungld/prometheus-exporter": "^1.1|^2.0"
    },
```

- [ ] **Step 2: Edit the `require-dev` block**

Change the `require-dev` block from:

```json
    "require-dev": {
        "mockery/mockery": "^1.4",
        "orchestra/testbench": "^4.0|^5.0|^6.0|^7.0|^8.0|^9.0|^10.0",
        "phpunit/phpunit": "^9.0|^10.0|^11.0"
    },
```

to:

```json
    "require-dev": {
        "mockery/mockery": "^1.4",
        "orchestra/testbench": "^4.0|^5.0|^6.0|^7.0|^8.0|^9.0|^10.0|^11.0",
        "phpunit/phpunit": "^9.0|^10.0|^11.0|^12.0"
    },
```

- [ ] **Step 3: Validate the composer file**

Run: `composer validate --no-check-publish`
Expected: `./composer.json is valid`

- [ ] **Step 4: Commit**

```bash
git add composer.json
git commit -m "Update: widen Composer constraints for Laravel 13

Add illuminate/support ^13.0, orchestra/testbench ^11.0, and
phpunit/phpunit ^12.0. Widen the parent vntrungld/prometheus-exporter
constraint to ^1.1|^2.0 since its Laravel 13 support shipped as a new
major version. PHP floor is unchanged (^7.2|^8.0 already covers 8.3-8.5).

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

### Task 2: Verify the Laravel 13 dependency set resolves and tests pass

**Files:**
- No file changes. This task is a verification gate that the Task 1 constraints actually resolve and the suite is green against Laravel 13.

**Interfaces:**
- Consumes: the widened constraints from Task 1.
- Produces: confidence (recorded evidence) that Laravel 13 + testbench 11 + phpunit 12 resolve and pass. No code artifact. If this task fails, it surfaces a real incompatibility before CI is touched.

- [ ] **Step 1: Pin the Laravel 13 dependency set without updating yet**

Run:

```bash
composer require --dev --no-update \
  "illuminate/support:13.*" \
  "orchestra/testbench:11.*" \
  "phpunit/phpunit:12.*"
```

Expected: command succeeds, no resolution performed yet (`--no-update`).

- [ ] **Step 2: Resolve the dependency set**

Run: `composer update --prefer-dist --no-interaction --no-progress`
Expected: resolution succeeds; `vntrungld/prometheus-exporter` resolves to a `2.x` version and `illuminate/support` to `13.x`. If resolution fails, STOP — the parent `^2.0` may not be published or may not allow Laravel 13; report this before continuing.

- [ ] **Step 3: Confirm resolved versions**

Run: `composer show illuminate/support vntrungld/prometheus-exporter orchestra/testbench phpunit/phpunit | grep -E "^name|^versions"`
Expected: `illuminate/support` 13.x, `vntrungld/prometheus-exporter` 2.x, `orchestra/testbench` 11.x, `phpunit/phpunit` 12.x.

- [ ] **Step 4: Run the test suite**

Run: `vendor/bin/phpunit`
Expected: PASS (all tests green) with no errors. If a test fails due to a genuine Laravel 13 incompatibility in `src/`, STOP and report — fixing `src/` is outside this plan's assumptions and needs a design revisit.

- [ ] **Step 5: Restore the committed dependency state**

The `composer require` in Step 1 may have re-pinned dev constraints in `composer.json`. Verify `composer.json` still matches Task 1's intended constraints:

Run: `git diff composer.json`
Expected: NO diff. If `composer require` altered the constraints, run `git checkout composer.json` to restore the Task 1 version (the wide constraints are what we ship; the Laravel 13 pin was only for local verification).

- [ ] **Step 6: Discard the local lock changes**

This package is a library and does not commit `composer.lock` (confirm it is gitignored).

Run: `git status --short`
Expected: working tree clean (no `composer.json` or tracked `composer.lock` changes). No commit needed for this task — it is a verification gate only.

---

### Task 3: Add the Laravel 13 CI matrix block

**Files:**
- Modify: `.github/workflows/tests.yml:75-87`

**Interfaces:**
- Consumes: the constraints from Task 1 (CI installs via `composer require "illuminate/support:..." "orchestra/testbench:..." "phpunit/phpunit:..."`).
- Produces: a CI matrix that exercises Laravel 13 on PHP 8.3 and 8.4.

- [ ] **Step 1: Append the Laravel 13 block to the matrix**

In `.github/workflows/tests.yml`, locate the end of the Laravel 12 block (the last matrix entry, currently ending at line 87 with the PHP 8.4 / Laravel 12 row). Immediately after that entry, add:

```yaml
          # Laravel 13
          - php: '8.3'
            laravel: '13.*'
            testbench: '11.*'
            phpunit: '12.*'
          - php: '8.4'
            laravel: '13.*'
            testbench: '11.*'
            phpunit: '12.*'
```

Match the existing two-space-per-level indentation exactly (matrix entries are indented 10 spaces under `include:`).

- [ ] **Step 2: Validate YAML syntax**

Run: `php -r "var_dump(yaml_parse_file('.github/workflows/tests.yml') !== false);"` — and if the `yaml` extension is unavailable, instead run: `python3 -c "import yaml,sys; yaml.safe_load(open('.github/workflows/tests.yml')); print('OK')"`
Expected: `bool(true)` or `OK` — the file parses as valid YAML.

- [ ] **Step 3: Confirm exactly two new Laravel 13 rows**

Run: `grep -c "laravel: '13.\*'" .github/workflows/tests.yml`
Expected: `2`

- [ ] **Step 4: Commit**

```bash
git add .github/workflows/tests.yml
git commit -m "Update: add Laravel 13 to CI test matrix

Add two matrix rows (PHP 8.3 and 8.4) running Laravel 13 with
Orchestra Testbench 11 and PHPUnit 12. Existing Laravel 6-12 rows
are unchanged.

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

### Task 4: Update the README compatibility table

**Files:**
- Modify: `readme.md:12-20`

**Interfaces:**
- Consumes: nothing (documentation only).
- Produces: a Requirements table listing Laravel 13.

- [ ] **Step 1: Add the Laravel 13 row**

In `readme.md`, change the Requirements table from:

```markdown
| Laravel | PHP       | Package |
|---------|-----------|---------|
| 6.x     | 7.2 - 8.0 | 1.x     |
| 7.x     | 7.2 - 8.0 | 1.x     |
| 8.x     | 7.3 - 8.1 | 1.x     |
| 9.x     | 8.0 - 8.2 | 1.x     |
| 10.x    | 8.1 - 8.3 | 1.x     |
| 11.x    | 8.2 - 8.4 | 1.x     |
| 12.x    | 8.2 - 8.4 | 1.x     |
```

to:

```markdown
| Laravel | PHP       | Package |
|---------|-----------|---------|
| 6.x     | 7.2 - 8.0 | 1.x     |
| 7.x     | 7.2 - 8.0 | 1.x     |
| 8.x     | 7.3 - 8.1 | 1.x     |
| 9.x     | 8.0 - 8.2 | 1.x     |
| 10.x    | 8.1 - 8.3 | 1.x     |
| 11.x    | 8.2 - 8.4 | 1.x     |
| 12.x    | 8.2 - 8.4 | 1.x     |
| 13.x    | 8.3 - 8.5 | 2.x     |
```

(The `Package` column refers to the parent `vntrungld/prometheus-exporter` line; Laravel 13 uses the new `2.x` major.)

- [ ] **Step 2: Confirm the row was added**

Run: `grep -n "13.x" readme.md`
Expected: one match showing `| 13.x    | 8.3 - 8.5 | 2.x     |`.

- [ ] **Step 3: Commit**

```bash
git add readme.md
git commit -m "Update: document Laravel 13 in README requirements table

Add a Laravel 13 row (PHP 8.3-8.5, parent package 2.x) to the
compatibility table.

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

## Self-Review

**1. Spec coverage:**
- composer.json parent constraint `^1.1|^2.0` → Task 1, Step 1. ✓
- composer.json `illuminate/support ^13.0` → Task 1, Step 1. ✓
- composer.json PHP unchanged → Global Constraints + Task 1 (left as-is). ✓
- composer.json testbench `^11.0`, phpunit `^12.0` → Task 1, Step 2. ✓
- CI Laravel 13 block (two rows) → Task 3. ✓
- README Laravel 13 → Task 4. ✓
- Verification (resolve + phpunit green) → Task 2. ✓
- Out of scope (no src changes, no parent package work) → honored; Task 2 STOPs if src incompatibility found. ✓

**2. Placeholder scan:** No TBD/TODO/"handle edge cases"/vague steps. Every code step shows exact before/after content. ✓

**3. Type consistency:** No code types involved (config + YAML + docs only). Constraint strings are identical across Global Constraints, Task 1, and Task 3. ✓
