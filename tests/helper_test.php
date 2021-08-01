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
 * Testing methods in helper class.
 *
 * @package     mod_marvel
 * @copyright   2021 Guillermo Gomez <guigomar@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_marvel;

defined('MOODLE_INTERNAL') || die();

class helper_test extends \advanced_testcase {

    /**
     *  Test get_categories_populated function in helper class.
     */
    public function test_connection(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $modmarvel = $this->getDataGenerator()->create_module('marvel', ['course' => $course->id]);

        // To test connection constant should be set up in config.php.
        if (defined('MARVELTESTPRIVATEKEY') && defined('MARVELTESTPRIVATEKEY')) {
            set_config('privatekey',MARVELTESTPRIVATEKEY, 'mod_marvel');
            set_config('publickey',MARVELTESTPUBLICKEY, 'mod_marvel');

            $marvellist = helper::get_marvel_list('characters', null, 5, 0);
            if (isset($marvellist->code)) {
                // Test that status is Ok.
                $this->assertEquals($marvellist->status, 'Ok');
                // Test that we are getting 5 records.
                $this->assertCount(5, $marvellist->data->results);
            }

            $marvellist = helper::get_marvel_list('comics', null, 101, 0);
            if (isset($marvellist->code)) {
                // Test that code is limit error.
                $this->assertEquals($marvellist->code, 409);
                // Test limit exceeded.
                $this->assertEquals($marvellist->status, 'You may not request more than 100 items.');
            }

        }

        // Test wrong API keys.
        set_config('privatekey',1, 'mod_marvel');
        set_config('publickey',1, 'mod_marvel');

        $marvellist = helper::get_marvel_list('characters', null, 5, 0);
        $this->assertEquals($marvellist->code, 'InvalidCredentials');
        $this->assertEquals($marvellist->message, 'The passed API key is invalid.');
    }
}
