<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Installation migration from auth_bruteforce.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Install local_bruteforce and migrate legacy auth_bruteforce data when available.
 *
 * @return void
 */
function xmldb_local_bruteforce_install(): void {
    global $DB;

    $legacylimit = (int)get_config('auth/bruteforce', 'limit');
    $legacyhours = (int)get_config('auth/bruteforce', 'hours');

    if ($legacylimit > 0) {
        set_config('iplimit', $legacylimit, 'local_bruteforce');
        set_config('identitylimit', $legacylimit, 'local_bruteforce');
    }

    if ($legacyhours > 0) {
        set_config('window', $legacyhours * HOURSECS, 'local_bruteforce');
        set_config('blockduration', $legacyhours * HOURSECS, 'local_bruteforce');
    }

    $dbman = $DB->get_manager();
    $legacytable = new xmldb_table('bruteforce_ip');
    if (!$dbman->table_exists($legacytable)) {
        return;
    }

    $window = $legacyhours > 0 ? $legacyhours * HOURSECS : DAYSECS;
    $limit = $legacylimit > 0 ? $legacylimit : 20;
    $since = time() - $window;

    $legacyrecords = $DB->get_records_select(
        'bruteforce_ip',
        'time >= :since',
        ['since' => $since],
        'ip ASC, time ASC',
        'id, ip, time'
    );

    $byip = [];
    foreach ($legacyrecords as $legacyrecord) {
        $ip = (string)$legacyrecord->ip;
        if ($ip === '') {
            continue;
        }
        $byip[$ip][] = (int)$legacyrecord->time;
    }

    foreach ($byip as $ip => $times) {
        $attempts = count($times);
        $blockeduntil = 0;

        if ($attempts >= $limit) {
            // The legacy plugin blocked while at least $limit attempts remained inside its rolling window.
            // The attempt at this position is the one whose expiry makes the legacy counter drop below the limit.
            $expiryindex = $attempts - $limit;
            $blockeduntil = $times[$expiryindex] + $window;
            if ($blockeduntil <= time()) {
                $blockeduntil = 0;
            }
        }

        $newrecord = (object)[
            'scope' => 'ip',
            'keyhash' => hash('sha256', 'ip' . "\0" . $ip . "\0"),
            'ip' => $ip,
            'usernamehash' => null,
            'attempts' => $attempts,
            'firstattempt' => reset($times),
            'lastattempt' => end($times),
            'blockeduntil' => $blockeduntil,
        ];

        if (!$DB->record_exists('local_bruteforce_attempt', [
            'scope' => 'ip',
            'keyhash' => $newrecord->keyhash,
        ])) {
            $DB->insert_record('local_bruteforce_attempt', $newrecord);
        }
    }
}
