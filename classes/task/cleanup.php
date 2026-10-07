<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_bruteforce\task;

/**
 * Remove expired brute-force counters.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup extends \core\task\scheduled_task {
    /**
     * Return the task name.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskcleanup', 'local_bruteforce');
    }

    /**
     * Execute cleanup.
     *
     * @return void
     */
    public function execute(): void {
        \local_bruteforce\manager::cleanup();
    }
}
