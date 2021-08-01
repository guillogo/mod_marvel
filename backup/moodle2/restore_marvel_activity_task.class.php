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
 * The task that provides a complete restore of mod_marvel is defined here.
 *
 * @package     mod_marvel
 * @category    backup
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/marvel/backup/moodle2/restore_marvel_stepslib.php');

/**
 * Restore task for mod_marvel.
 */
class restore_marvel_activity_task extends restore_activity_task {

    /**
     * Defines particular settings that this activity can have.
     */
    protected function define_my_settings() : void {
        // No particular settings for this activity.
    }

    /**
     * Defines particular steps that this activity can have.
     *
     * @return void.
     * @throws base_task_exception
     */
    protected function define_my_steps(): void {
        $this->add_step(new restore_marvel_activity_structure_step('marvel_structure', 'marvel.xml'));
    }

    /**
     * Defines the contents in the activity that must be processed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_contents(): array {
        $contents = [];

        // Define the contents.
        $contents[] = new restore_decode_content('marvel', ['intro'], 'marvel');

        return $contents;
    }

    /**
     * Defines the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return restore_decode_rule[].
     */
    public static function define_decode_rules(): array {
        $rules = [];

        $rules[] = new restore_decode_rule('MARVELVIEWBYID', '/mod/marvel/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('MARVELINDEX', '/mod/marvel/index.php?id=$1', 'course');

        return $rules;
    }

    /**
     * Defines the restore log rules that will be applied by the
     * {@see restore_logs_processor} when restoring mod_marvel logs. It
     * must return one array of {@see restore_log_rule} objects.
     *
     * @return restore_log_rule[].
     */
    public static function define_restore_log_rules(): array {
        $rules = [];

        // Define the rules.
        $rules[] = new restore_log_rule('marvel', 'add', 'view.php?id={course_module}', '{marvel}');
        $rules[] = new restore_log_rule('marvel', 'update', 'view.php?id={course_module}', '{marvel}');
        $rules[] = new restore_log_rule('marvel', 'view', 'view.php?id={course_module}', '{marvel}');

        return $rules;
    }
}
