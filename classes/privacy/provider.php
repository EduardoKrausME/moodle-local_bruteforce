<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_bruteforce\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy metadata provider.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider {
    /**
     * Describe stored security metadata.
     *
     * @param collection $collection Privacy metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'local_bruteforce_attempt',
            [
                'ip' => 'privacy:metadata:attempt:ip',
                'usernamehash' => 'privacy:metadata:attempt:usernamehash',
                'attempts' => 'privacy:metadata:attempt:attempts',
                'firstattempt' => 'privacy:metadata:attempt:firstattempt',
                'lastattempt' => 'privacy:metadata:attempt:lastattempt',
                'blockeduntil' => 'privacy:metadata:attempt:blockeduntil',
            ],
            'privacy:metadata:attempt'
        );

        return $collection;
    }
}
