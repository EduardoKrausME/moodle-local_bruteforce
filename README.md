# Brute Force Detect

Brute Force Detect helps protect Moodle accounts against repeated password guesses. It limits unsuccessful login attempts before an attacker can keep trying indefinitely, while allowing administrators to set different limits for individual accounts and shared networks.

The protection applies to regular Moodle sign-ins and login requests used by web services. It works alongside Moodle's existing authentication methods rather than replacing them.

## Why use it?

A single public IP address may represent hundreds of legitimate users in a university, school or company. Blocking the entire address after a handful of failed passwords can lock out a whole campus because one person mistyped a password. At the same time, a high limit applied to every account gives attackers too many opportunities to target one username.

Brute Force Detect addresses both situations with separate limits for attempts against an individual username from one IP address and for overall unsuccessful attempts from that IP address.

## Main features

- **Account-focused protection:** temporarily blocks repeated failed attempts against the same username from the same IP address.
- **Shared-network awareness:** lets administrators apply a different, broader threshold to an IP address used by many people.
- **Web-service login protection:** applies the same protection to the token-based login endpoint.
- **Temporary restrictions:** allows another attempt after the configured blocking period instead of permanently denying access.
- **Automatic cleanup:** removes expired attempt records so temporary security data does not accumulate indefinitely.
- **Successful-login handling:** clears the focused failed-attempt counter after a successful sign-in.

## Administration settings

Administrators can adjust four settings from the plugin's page in Site administration:

| Setting | Default | Purpose |
| --- | --- | --- |
| Attempts per username and IP | 5 | Limits repeated failures against one username from one address. |
| Attempts per IP | 50 | Limits failures across multiple usernames from the same address. |
| Tracking window | 1 hour | Defines how far back unsuccessful attempts are counted. |
| Blocking duration | 1 hour | Sets how long a threshold-triggered restriction lasts. |

These defaults are a starting point, not a universal security policy. A campus with shared internet access may need a different overall IP limit from a small institution where staff connect from separate addresses.

## Existing Brute Force protection

Sites previously using the older `auth_bruteforce` plugin can retain useful configuration and recent protection information during migration to Brute Force Detect. The old data is not deleted as part of that transition.

Administrators should review the resulting thresholds, particularly when moving between plugins or changing how users reach the site.

## Privacy and operational notes

The plugin does not store submitted usernames as plain text in its account-focused counters. It retains source IP addresses temporarily because they are needed to identify the origin of repeated attempts.

Brute Force Detect does not replace strong passwords, multifactor authentication, properly configured proxies or security monitoring. Moodle's scheduled tasks should run regularly so expired counters are removed.

**Compatibility:** Moodle 4.1 or newer.

**License:** GNU GPL v3 or later.
