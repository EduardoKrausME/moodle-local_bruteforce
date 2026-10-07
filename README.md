# Brute Force Detect

`local_bruteforce` protects Moodle authentication endpoints against repeated password attempts without acting as an authentication method itself.

The plugin observes `core\event\user_login_failed` and keeps compact rolling counters instead of storing one permanent database row for every failed login. Two counters are maintained independently: one for the whole remote IP and another for the IP+username pair. This allows a stricter limit against a focused attack while keeping a higher broad IP limit for campuses, companies and other networks where many legitimate users share the same public address.

When a counter reaches its configured limit, subsequent login requests are rejected before Moodle verifies another password. The protection covers the normal web login and the token endpoint used by web services. A scheduled task removes expired counters so attack traffic does not turn the security table into an ever-growing log.

The submitted username is not stored in plain text. The plugin stores a one-way hash only for the IP+username counter, while the remote IP is retained temporarily because it is the source being rate-limited.

When installed on a site that previously used `auth_bruteforce`, the installer reads the old `limit` and `hours` settings and imports recent IP counters from the legacy `bruteforce_ip` table. The legacy table is deliberately left untouched so an old authentication plugin that still exists during migration is not broken.
