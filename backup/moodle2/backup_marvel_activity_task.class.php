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
 * The task that provides all the steps to perform a complete backup is defined here.
 *
 * @package     mod_marvel
 * @category    backup
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/marvel/backup/moodle2/backup_marvel_stepslib.php');

/**
 * Choice backup task that provides all the settings and steps to perform one complete backup of the activity.
 */
class backup_marvel_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Marvel only has one structure step.
        $this->add_step(new backup_marvel_activity_structure_step('marvel_structure', 'marvel.xml'));

    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of marvels.
        $search = "/(".$base."\/mod\/marvel\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@MARVELINDEX*$2@$', $content);

        // Link to marvel view by moduleid.
        $search = "/(".$base."\/mod\/marvel\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@MARVELVIEWBYID*$2@$', $content);

        return $content;
    }
}
