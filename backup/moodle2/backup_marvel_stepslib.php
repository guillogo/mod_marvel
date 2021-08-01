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
 * Backup steps for mod_marvel are defined here.
 *
 * @package     mod_marvel
 * @category    backup
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_marvel_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $marvel = new backup_nested_element('marvel', ['id'],
            [
                'name',
                'timecreated',
                'timemodified',
                'intro',
                'introformat',
                'list',
            ]
        );

        // Build the tree
        // (No tree).

        // Define sources.
        $marvel->set_source_table('marvel', ['id' => backup::VAR_ACTIVITYID]);

        // Define id annotations
        // (none).

        // Define file annotations.
        $marvel->annotate_files('mod_marvel', 'intro', null); // This file area does not have an itemid.

        // Return the root element (marvel), wrapped into standard activity structure.
        return $this->prepare_activity_structure($marvel);
    }
}
