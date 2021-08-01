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
 * Prints an instance of mod_marvel.
 *
 * @package     mod_marvel
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_marvel\helper;
use mod_marvel\table\marvel_table;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once('../../lib/tablelib.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$m = optional_param('m', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('marvel', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('marvel', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('marvel', array('id' => $m), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('marvel', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_marvel\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('marvel', $moduleinstance);
$event->trigger();

$url = new moodle_url('/mod/marvel/view.php', array('id' => $cm->id));
$download = optional_param('download', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);

$PAGE->set_url($url);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string(helper::get_list_options()[$moduleinstance->list]));
$PAGE->set_context($modulecontext);

// Get the Marvel list.
$marvellist =  helper::get_marvel_list($moduleinstance->list);
// If the status is OK display the data into the table.
if ($marvellist->code === helper::STATUSOK) {
    $table = new marvel_table('marvel_list', $url, $marvellist, $moduleinstance->list, $download, $page);
    if (!$table->is_downloading()) {
        echo $OUTPUT->header();
    }
    $table->out(10, false);

    if (!$table->is_downloading()) {
        // Display the copyright;
        echo $OUTPUT->render_from_template('mod_marvel/copyright', ['attributionhtml' => $marvellist->attributionHTML]);
        echo $OUTPUT->footer();
    }
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->notification($marvellist->code . ' - ' . $marvellist->message);
    echo $OUTPUT->footer();
}
