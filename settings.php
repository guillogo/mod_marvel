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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     mod_marvel
 * @category    admin
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage('mod_marvel', get_string('pluginname', 'mod_marvel'), 'moodle/site:config');

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_heading('mod_marvel/api',
            new lang_string('settings:api:header', 'mod_marvel'), ''));

        $settings->add(new admin_setting_configtext('mod_marvel/publickey',
            get_string('settings:publickey', 'mod_marvel' ),
            get_string('settings:publickey_desc', 'mod_marvel'),
            '', PARAM_TEXT));

        $settings->add(new admin_setting_configtext('mod_marvel/privatekey',
            get_string('settings:privatekey', 'mod_marvel' ),
            get_string('settings:privatekey_desc', 'mod_marvel'),
            '', PARAM_TEXT));
    }
}
