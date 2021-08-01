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

use curl;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class helper contains all library functions.
 *
 * @package mod_marvel
 */
class helper {

    /**
     * Defines the waiting for analysis status.
     */
    const ENDPOINT = "https://gateway.marvel.com/v1/public/";

    /**
     * Status OK.
     */
    const STATUSOK = 200;

    /**
     * Expiry period for caches.
     *
     */
    const DAY = 60 * 60 * 24; // One day.

    /**
     * Gets the list of choices to select by professor.
     *
     * @return array choices.
     */
    public static function get_list_options() : array {
        $choices =
            [
                'characters' => 'Characters',
                'comics' => 'Comics',
                'creators' => 'Creators',
                'events' => 'Events',
                'series' => 'Series',
                'stories' => 'Stories',
            ];
        return $choices;
    }

    /**
     * Gets the object with Marvel information.
     *
     * @param string $listtype Type on list (e.g.: characters)
     * @param string|null $additionaldata Marvel ID item
     * @param int $limit Limit value
     * @param int $offset offset value
     * @return stdClass Marvel list object.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_marvel_list(string $listtype, string $additionaldata = null,
                                           int $limit = 100, int $offset = 0): stdClass {
        $endpoint = self::ENDPOINT;
        $timestamp = time();
        $publickey = get_config('mod_marvel', 'publickey');
        $privatekey = get_config('mod_marvel', 'privatekey');
        $checksum = md5($timestamp . $privatekey . $publickey);

        $curl = new curl();
        $marvelid = null;
        if ($additionaldata) {
            $marvelid = '/' . $additionaldata;
        }
        $url = $endpoint . $listtype . $marvelid . '?ts=' . $timestamp . '&apikey=' . $publickey . '&hash=' . $checksum .
            '&limit=' . $limit . '&offset=' . $offset;

        // Try to get value from cache (Cache the data for 1 day).
        $date = strtotime(date("Y-m-d", time())) - self::DAY;
        $checksumtocache = md5('1' . $privatekey . $publickey);
        $cachekey = (string)$date . '_' . $listtype . '_' . $checksumtocache . '_' . $additionaldata;
        $cache = \cache::make('mod_marvel', 'listsbydate');
        $data = $cache->get($cachekey);

        if ($data && (time() < $data->expiry)) { // Valid cache data.
            $list = $data->list;
        } else {
            $list = json_decode($curl->get($url));

            // Update cache.
            if (!empty($list)) {
                $expiry = time() + self::DAY;
                $data = new \stdClass();
                $data->expiry = $expiry;
                $data->list = $list;
                $cache->set($cachekey, $data);
            }
        }

        return $list;
    }

    /**
     * Gets the thumbnail url from an object.
     *
     * @param stdClass $thumbnail Thumbnail object
     * @return \moodle_url|null Thumbnail url.
     */
    public static function get_thumbnail_url(stdClass $thumbnail): ?\moodle_url {
        if (isset($thumbnail->path) && isset($thumbnail->extension)) {
            return new \moodle_url($thumbnail->path . '.' . $thumbnail->extension);
        }
        return null;
    }
}
