<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_bruteforce;

/**
 * Hook callbacks.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Guard authentication requests after Moodle configuration is loaded.
     *
     * @param \core\hook\after_config $hook Hook instance.
     * @return void
     */
    public static function after_config(\core\hook\after_config $hook): void {
        manager::guard_request();
    }
}
