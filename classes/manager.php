<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * classes/manager.php for local_bruteforce.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_bruteforce;

use dml_write_exception;

/**
 * Brute-force protection manager.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** Scope used for broad IP protection. */
    private const SCOPE_IP = 'ip';

    /** Scope used for a single username coming from a single IP. */
    private const SCOPE_IDENTITY = 'identity';

    /**
     * Guard supported authentication endpoints before credentials are checked.
     *
     * @return void
     */
    public static function guard_request(): void {
        global $CFG, $SCRIPT;

        if (during_initial_install() || isset($CFG->upgraderunning) || CLI_SCRIPT) {
            return;
        }

        if (get_config('local_bruteforce', 'version') === false) {
            return;
        }

        $script = $SCRIPT ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? '';

        if ($script === '/login/index.php') {
            if ($method !== 'POST') {
                return;
            }
        } else if ($script === '/login/token.php') {
            if ($method !== 'POST') {
                return;
            }
        } else {
            return;
        }

        $ip = getremoteaddr();
        if ($ip === '') {
            return;
        }

        $username = optional_param('username', '', PARAM_RAW_TRIMMED);
        $blockeduntil = self::get_blocked_until($ip, $username);
        if ($blockeduntil <= time()) {
            return;
        }

        $remaining = max(1, $blockeduntil - time());
        $message = get_string('blocked', 'local_bruteforce', format_time($remaining));

        if ($script === '/login/token.php') {
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json; charset=utf-8');
            header('Retry-After: ' . $remaining);
            http_response_code(429);
            echo json_encode([
                'error' => $message,
                'errorcode' => 'local_bruteforce_blocked',
            ]);
            exit;
        }

        throw new \moodle_exception('blocked', 'local_bruteforce', '', format_time($remaining));
    }

    /**
     * Record a failed authentication attempt.
     *
     * @param string $ip Remote IP address.
     * @param string $username Submitted username, when available.
     * @return void
     */
    public static function register_failure(string $ip, string $username = ''): void {
        if ($ip === '') {
            return;
        }

        $now = time();
        self::record_scope(self::SCOPE_IP, $ip, '', self::get_ip_limit(), $now);

        $usernamehash = self::hash_username($username);
        if ($usernamehash !== '') {
            self::record_scope(self::SCOPE_IDENTITY, $ip, $usernamehash, self::get_identity_limit(), $now);
        }
    }

    /**
     * Clear the focused IP+username counter after a successful web login.
     * The broad IP counter is intentionally preserved because it represents activity across accounts.
     *
     * @param string $ip Remote IP address.
     * @param string $username Authenticated username.
     * @return void
     */
    public static function clear_identity(string $ip, string $username): void {
        global $DB;

        $usernamehash = self::hash_username($username);
        if ($ip === '' || $usernamehash === '') {
            return;
        }

        $DB->delete_records('local_bruteforce_attempt', [
            'scope' => self::SCOPE_IDENTITY,
            'keyhash' => self::make_key(self::SCOPE_IDENTITY, $ip, $usernamehash),
        ]);
    }

    /**
     * Return the timestamp until which the request is blocked.
     *
     * @param string $ip Remote IP address.
     * @param string $username Submitted username, when available.
     * @return int
     */
    public static function get_blocked_until(string $ip, string $username = ''): int {
        global $DB;

        if ($ip === '') {
            return 0;
        }

        $blockeduntil = 0;
        $ipkey = self::make_key(self::SCOPE_IP, $ip, '');
        $iprecord = $DB->get_record('local_bruteforce_attempt', [
            'scope' => self::SCOPE_IP,
            'keyhash' => $ipkey,
        ], 'blockeduntil', IGNORE_MISSING);
        if ($iprecord) {
            $blockeduntil = max($blockeduntil, (int)$iprecord->blockeduntil);
        }

        $usernamehash = self::hash_username($username);
        if ($usernamehash !== '') {
            $identitykey = self::make_key(self::SCOPE_IDENTITY, $ip, $usernamehash);
            $identityrecord = $DB->get_record('local_bruteforce_attempt', [
                'scope' => self::SCOPE_IDENTITY,
                'keyhash' => $identitykey,
            ], 'blockeduntil', IGNORE_MISSING);
            if ($identityrecord) {
                $blockeduntil = max($blockeduntil, (int)$identityrecord->blockeduntil);
            }
        }

        return $blockeduntil;
    }

    /**
     * Remove stale counters.
     *
     * @return void
     */
    public static function cleanup(): void {
        global $DB;

        $retention = max(self::get_window(), self::get_block_duration()) * 2;
        $threshold = time() - max($retention, DAYSECS);

        $DB->delete_records_select(
            'local_bruteforce_attempt',
            'lastattempt < :threshold AND blockeduntil < :now',
            [
                'threshold' => $threshold,
                'now' => time(),
            ]
        );
    }

    /**
     * Store a failure counter for one scope.
     *
     * @param string $scope Counter scope.
     * @param string $ip Remote IP address.
     * @param string $usernamehash Pseudonymised username.
     * @param int $limit Maximum failures before blocking.
     * @param int $now Current timestamp.
     * @return void
     */
    private static function record_scope(
        string $scope,
        string $ip,
        string $usernamehash,
        int $limit,
        int $now
    ): void {
        global $DB;

        $keyhash = self::make_key($scope, $ip, $usernamehash);
        $params = [
            'scope' => $scope,
            'keyhash' => $keyhash,
        ];
        $record = $DB->get_record('local_bruteforce_attempt', $params, '*', IGNORE_MISSING);

        if (!$record) {
            $record = (object)[
                'scope' => $scope,
                'keyhash' => $keyhash,
                'ip' => $ip,
                'usernamehash' => $usernamehash,
                'attempts' => 1,
                'firstattempt' => $now,
                'lastattempt' => $now,
                'blockeduntil' => $limit <= 1 ? $now + self::get_block_duration() : 0,
            ];

            try {
                $DB->insert_record('local_bruteforce_attempt', $record);
                return;
            } catch (dml_write_exception $exception) {
                // A concurrent request may have inserted the same unique scope/key pair.
                $record = $DB->get_record('local_bruteforce_attempt', $params, '*', MUST_EXIST);
            }
        }

        if ((int)$record->blockeduntil > $now) {
            $record->lastattempt = $now;
            $DB->update_record('local_bruteforce_attempt', $record);
            return;
        }

        if ((int)$record->lastattempt < $now - self::get_window()) {
            $record->attempts = 1;
            $record->firstattempt = $now;
            $record->blockeduntil = 0;
        } else {
            $record->attempts = (int)$record->attempts + 1;
        }

        $record->lastattempt = $now;
        if ((int)$record->attempts >= $limit) {
            $record->blockeduntil = $now + self::get_block_duration();
        }

        $DB->update_record('local_bruteforce_attempt', $record);
    }

    /**
     * Build a stable unique key without exposing the username in the key itself.
     *
     * @param string $scope Counter scope.
     * @param string $ip Remote IP address.
     * @param string $usernamehash Pseudonymised username.
     * @return string
     */
    private static function make_key(string $scope, string $ip, string $usernamehash): string {
        return hash('sha256', $scope . "\0" . $ip . "\0" . $usernamehash);
    }

    /**
     * Pseudonymise a username before storing it.
     *
     * @param string $username Username.
     * @return string
     */
    private static function hash_username(string $username): string {
        $username = trim($username);
        if ($username === '') {
            return '';
        }

        return hash('sha256', \core_text::strtolower($username));
    }

    /**
     * Get the per-IP failure limit.
     *
     * @return int
     */
    private static function get_ip_limit(): int {
        $value = (int)get_config('local_bruteforce', 'iplimit');
        return $value > 0 ? $value : 50;
    }

    /**
     * Get the IP+username failure limit.
     *
     * @return int
     */
    private static function get_identity_limit(): int {
        $value = (int)get_config('local_bruteforce', 'identitylimit');
        return $value > 0 ? $value : 5;
    }

    /**
     * Get the rolling failure window in seconds.
     *
     * @return int
     */
    private static function get_window(): int {
        $value = (int)get_config('local_bruteforce', 'window');
        return $value > 0 ? $value : HOURSECS;
    }

    /**
     * Get the block duration in seconds.
     *
     * @return int
     */
    private static function get_block_duration(): int {
        $value = (int)get_config('local_bruteforce', 'blockduration');
        return $value > 0 ? $value : HOURSECS;
    }
}
