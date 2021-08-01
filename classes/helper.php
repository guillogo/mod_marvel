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
 * Library functions used by db/install.php.
 *
 * @package     mod_marvel
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_marvel;

defined('MOODLE_INTERNAL') || die();

/**
 * Class helper contains all library functions.
 *
 * @package mod_marvel
 */
class helper {

    /**
     * Gets the list of choices to select by professor.
     *
     */
    public static function get_list_options() {
        $choices =
            [
                'characters' => 'Characters',
                'comics' => 'Comics',
                'crators' => 'Crators',
                'events' => 'Events',
                'series' => 'Series',
                'stories' => 'Stories',

            ];
        return $choices;
    }

}