<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * classes/observer.php for local_bruteforce.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_bruteforce;

/**
 * Moodle event observers.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Record failed login attempts.
     *
     * @param \core\event\user_login_failed $event Failed login event.
     * @return void
     */
    public static function user_login_failed(\core\event\user_login_failed $event): void {
        $username = '';
        if (!empty($event->other['username'])) {
            $username = (string)$event->other['username'];
        }

        manager::register_failure(getremoteaddr(), $username);
    }

    /**
     * Clear the focused counter after a successful login.
     *
     * @param \core\event\user_loggedin $event Successful login event.
     * @return void
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        global $DB;

        $username = '';
        if (!empty($event->other['username'])) {
            $username = (string)$event->other['username'];
        } else if (!empty($event->userid)) {
            $username = (string)$DB->get_field('user', 'username', ['id' => $event->userid]);
        }

        manager::clear_identity(getremoteaddr(), $username);
    }
}
