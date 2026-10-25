# Changelog

This changelog documents only the two most recent **published** releases of `local_bruteforce`, checked on October 24, 2026. Each entry reflects the net file differences between a published release tag and its immediately preceding published tag. The `-core_text` commit made on October 21 after the `2.0.5` tag is intentionally excluded.

## [2.0.5] - 2026-10-21

### Code quality and Moodle compatibility
- Normalized Moodle GPL boilerplate headers across PHP source, configuration, language, task, hook and event files.
- Updated PHP namespace imports and type references: imported the `after_config` hook in `classes/hook_callbacks.php` and `db/hooks.php`, imported login event classes in `classes/observer.php`, and used explicit imports for the scheduled-task base class and plugin manager in `classes/task/cleanup.php`.
- Replaced fully qualified references with imported equivalents for `core_text` and `moodle_exception` in `classes/manager.php`, and for `core\plugininfo\auth` in `db/install.php`. These are code-style changes rather than new brute-force prevention features.
- Removed the `defined('MOODLE_INTERNAL') || die();` guard from `db/install.php` and `lib.php` in the historical tag comparison while adjusting their `use` declarations.
- Refreshed the plugin's PHP header formatting and metadata to internal version `2026102000`, release `2.0.5`.

**Full comparison:** [2.0.4...2.0.5](https://github.com/EduardoKrausME/moodle-local_bruteforce/compare/2.0.4...2.0.5)

## [2.0.4] - 2026-10-15

### Release metadata
- Updated `version.php` from internal version `2026101502` and release `2.0.3` to internal version `2026101503` and release `2.0.4`.
- The three commits in this comparison include intermediate release-workflow changes, but the **net published-tag diff** contains only `version.php`; no application or workflow file differs between the two tags.

**Full comparison:** [2.0.3...2.0.4](https://github.com/EduardoKrausME/moodle-local_bruteforce/compare/2.0.3...2.0.4)
