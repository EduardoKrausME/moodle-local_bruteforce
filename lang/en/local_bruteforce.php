<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * English language strings.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['blockduration'] = 'Block duration';
$string['blockduration_desc'] = 'How long an IP or an IP+username pair remains blocked after reaching its failure limit.';
$string['blocked'] = 'Too many failed login attempts were detected from your connection. Try again in {$a}.';
$string['identitylimit'] = 'Attempts per IP and username';
$string['identitylimit_desc'] = 'Maximum failed attempts for the same username from the same IP inside the rolling window. This catches focused attacks without blocking every user behind a shared NAT.';
$string['iplimit'] = 'Attempts per IP';
$string['iplimit_desc'] = 'Maximum failed attempts from one IP inside the rolling window, regardless of username. Keep this limit higher than the IP+username limit on networks where many users share the same public IP.';
$string['pluginname'] = 'Brute Force Detect';
$string['privacy:metadata:attempt'] = 'Temporary security counters used to detect repeated failed authentication attempts.';
$string['privacy:metadata:attempt:attempts'] = 'Number of failed authentication attempts counted in the current window.';
$string['privacy:metadata:attempt:blockeduntil'] = 'Timestamp until which the source is blocked.';
$string['privacy:metadata:attempt:firstattempt'] = 'Timestamp of the first failed attempt in the current window.';
$string['privacy:metadata:attempt:ip'] = 'Remote IP address from which failed authentication attempts were received.';
$string['privacy:metadata:attempt:lastattempt'] = 'Timestamp of the latest failed authentication attempt.';
$string['privacy:metadata:attempt:usernamehash'] = 'One-way hash of the submitted username, used to distinguish focused attacks without storing the username in plain text.';
$string['settingsheading'] = 'Login protection';
$string['settingsheading_desc'] = 'The plugin observes failed Moodle logins and applies independent limits to an IP+username pair and to the whole IP address. Login requests are rejected before password verification while a counter is blocked.';
$string['taskcleanup'] = 'Clean expired brute-force counters';
$string['window'] = 'Attempt window';
$string['window_desc'] = 'Rolling period in which failed attempts are accumulated. After a quiet period longer than this window, the counter starts again.';
