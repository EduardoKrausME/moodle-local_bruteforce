<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Administration settings.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_bruteforce', get_string('pluginname', 'local_bruteforce'));
    $ADMIN->add('localplugins', $settings);

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_heading(
            'local_bruteforce/general',
            get_string('settingsheading', 'local_bruteforce'),
            get_string('settingsheading_desc', 'local_bruteforce')
        ));

        $settings->add(new admin_setting_configtext(
            'local_bruteforce/identitylimit',
            get_string('identitylimit', 'local_bruteforce'),
            get_string('identitylimit_desc', 'local_bruteforce'),
            5,
            PARAM_INT
        ));

        $settings->add(new admin_setting_configtext(
            'local_bruteforce/iplimit',
            get_string('iplimit', 'local_bruteforce'),
            get_string('iplimit_desc', 'local_bruteforce'),
            50,
            PARAM_INT
        ));

        $settings->add(new admin_setting_configduration(
            'local_bruteforce/window',
            get_string('window', 'local_bruteforce'),
            get_string('window_desc', 'local_bruteforce'),
            HOURSECS,
            HOURSECS
        ));

        $settings->add(new admin_setting_configduration(
            'local_bruteforce/blockduration',
            get_string('blockduration', 'local_bruteforce'),
            get_string('blockduration_desc', 'local_bruteforce'),
            HOURSECS,
            HOURSECS
        ));
    }
}
